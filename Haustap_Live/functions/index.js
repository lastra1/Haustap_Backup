const functions = require('firebase-functions');
const express = require('express');
const cors = require('cors');
const mysql = require('mysql2/promise');
const admin = require('firebase-admin');

const app = express();
admin.initializeApp();

// Enable CORS for all routes
app.use(cors({
  origin: true,
  credentials: true
}));

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Database configuration
const dbConfig = {
  host: functions.config().mysql?.host || 'localhost',
  port: functions.config().mysql?.port || 3307,
  user: functions.config().mysql?.user || 'haustap_user',
  password: functions.config().mysql?.password || 'haustap_password',
  database: functions.config().mysql?.database || 'haustap_db',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
};

// Create connection pool
const pool = mysql.createPool(dbConfig);

// Health check endpoint
app.get('/api/health', (req, res) => {
  res.json({ 
    status: 'OK', 
    service: 'HausTap API',
    timestamp: new Date().toISOString(),
    database: 'MySQL Connected'
  });
});

// Test database connection
app.get('/api/test-db', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = ?', [dbConfig.database]);
    res.json({
      status: 'Database Connected',
      tables: rows[0].table_count,
      config: {
        host: dbConfig.host,
        database: dbConfig.database,
        port: dbConfig.port
      }
    });
  } catch (error) {
    res.status(500).json({
      status: 'Database Connection Failed',
      error: error.message,
      config: {
        host: dbConfig.host,
        database: dbConfig.database,
        port: dbConfig.port
      }
    });
  }
});

// API Routes - Proxy your existing Laravel endpoints
app.get('/api/services', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM services LIMIT 10');
    res.json({
      success: true,
      data: rows,
      message: 'Services retrieved successfully'
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: 'Failed to retrieve services',
      error: error.message
    });
  }
});

app.get('/api/users', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT id, name, email, created_at FROM users LIMIT 10');
    res.json({
      success: true,
      data: rows,
      message: 'Users retrieved successfully'
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: 'Failed to retrieve users',
      error: error.message
    });
  }
});

app.get('/api/bookings', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM bookings LIMIT 10');
    res.json({
      success: true,
      data: rows,
      message: 'Bookings retrieved successfully'
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      message: 'Failed to retrieve bookings',
      error: error.message
    });
  }
});

async function verify(req) {
  const h = req.headers.authorization || '';
  const token = h.startsWith('Bearer ') ? h.slice(7) : '';
  const isEmu = process.env.FUNCTIONS_EMULATOR === 'true';
  if (isEmu && token.startsWith('uid:')) {
    return token.slice(4);
  }
  const decoded = await admin.auth().verifyIdToken(token);
  return decoded.uid;
}

app.post('/api/bookings', async (req, res) => {
  try {
    const uid = await verify(req);
    const { providerId, serviceName, scheduledDate, scheduledTime, address, total, notes } = req.body;
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      const sched = scheduledDate ? new Date(scheduledDate) : new Date();
      const pidNum = Number(providerId);
      const pid = providerId && Number.isFinite(pidNum) && pidNum > 0 ? pidNum : null;
      const cid = Number.isFinite(Number(uid)) ? Number(uid) : null;
      const [result] = await conn.execute(
        'INSERT INTO bookings (client_id, provider_id, service_id, status, scheduled_at, address, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
        [cid, pid, null, 'pending', sched, address || null, notes || null]
      );
      const bookingId = result.insertId.toString();
      const doc = {
        id: bookingId,
        userId: uid,
        clientUid: uid,
        providerId: providerId || null,
        providerUid: providerId || null,
        serviceName: serviceName || '',
        scheduledDate: sched,
        scheduledTime: scheduledTime || '',
        address: address || '',
        status: 'pending',
        total: total || 0,
        notes: notes || '',
        createdAt: new Date(),
        updatedAt: new Date()
      };
      await conn.commit();
      res.json({ id: bookingId, status: 'pending' });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) {
    res.status(401).json({ error: e.message });
  }
});

app.post('/api/bookings/:id/status', async (req, res) => {
  try {
    const uid = await verify(req);
    const id = req.params.id;
    const { status } = req.body;
    const snap = await admin.firestore().collection('bookings').doc(id).get();
    if (!snap.exists) return res.status(404).json({ error: 'not_found' });
    const b = snap.data();
    const isProvider = uid === (b.providerUid || b.providerId);
    const allowed = ['ongoing', 'completed'];
    if (!isProvider || !allowed.includes(status)) return res.status(403).json({ error: 'forbidden' });
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      await conn.execute('UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?', [status, id]);
      await conn.commit();
      res.json({ id, status });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) {
    res.status(401).json({ error: e.message });
  }
});

app.post('/api/bookings/:id/cancel', async (req, res) => {
  try {
    const uid = await verify(req);
    const id = req.params.id;
    const snap = await admin.firestore().collection('bookings').doc(id).get();
    if (!snap.exists) return res.status(404).json({ error: 'not_found' });
    const b = snap.data();
    const isClient = uid === (b.clientUid || b.userId);
    if (!isClient) return res.status(403).json({ error: 'forbidden' });
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      await conn.execute('UPDATE bookings SET status = ?, cancelled_at = NOW(), updated_at = NOW() WHERE id = ?', ['cancelled', id]);
      await conn.commit();
      res.json({ id, status: 'cancelled' });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) {
    res.status(401).json({ error: e.message });
  }
});

app.post('/api/bookings/:id/rate', async (req, res) => {
  try {
    const uid = await verify(req);
    const id = req.params.id;
    const { rating, notes } = req.body;
    const snap = await admin.firestore().collection('bookings').doc(id).get();
    if (!snap.exists) return res.status(404).json({ error: 'not_found' });
    const b = snap.data();
    const isClient = uid === (b.clientUid || b.userId);
    if (!isClient) return res.status(403).json({ error: 'forbidden' });
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      await conn.execute('UPDATE bookings SET rating = ?, notes = ?, rated_at = NOW(), updated_at = NOW() WHERE id = ?', [rating, notes || null, id]);
      await conn.commit();
      res.json({ id, rating });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) {
    res.status(401).json({ error: e.message });
  }
});

app.post('/api/chat/open', async (req, res) => {
  try {
    const uid = await verify(req);
    const { bookingId } = req.body;
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      const [brows] = await conn.query('SELECT id, client_id, provider_id FROM bookings WHERE id = ?', [bookingId]);
      if (!brows.length) { await conn.rollback(); return res.status(404).json({ error: 'not_found' }); }
      const b = brows[0];
      const client = b.client_id || null;
      const provider = b.provider_id || null;
      const [exist] = await conn.query('SELECT id FROM chats WHERE booking_id = ? LIMIT 1', [bookingId]);
      if (exist.length) { await conn.commit(); return res.json({ chatId: exist[0].id, created: false }); }
      const now = new Date();
      const [ins] = await conn.query('INSERT INTO chats (booking_id, user_one_id, user_two_id, last_message_at, last_message_by, last_message_text, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [bookingId, client, provider, now, uid, '', 'active']);
      await conn.commit();
      res.json({ chatId: ins.insertId, created: true });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) { res.status(401).json({ error: e.message }); }
});

app.get('/api/chat/:bookingId/messages', async (req, res) => {
  try {
    const uid = await verify(req);
    const { bookingId } = req.params;
    const conn = await pool.getConnection();
    try {
      const [crow] = await conn.query('SELECT id, user_one_id, user_two_id FROM chats WHERE booking_id = ? LIMIT 1', [bookingId]);
      if (!crow.length) { return res.status(404).json({ error: 'not_found' }); }
      const c = crow[0];
      if (![c.user_one_id, c.user_two_id].includes(Number(uid))) return res.status(403).json({ error: 'forbidden' });
      const [msgs] = await conn.query('SELECT id, chat_id, sender_id, text, created_at FROM chat_messages WHERE chat_id = ? ORDER BY created_at ASC LIMIT 100', [c.id]);
      res.json(msgs);
    } finally { conn.release(); }
  } catch (e) { res.status(401).json({ error: e.message }); }
});

app.post('/api/chat/:bookingId/messages', async (req, res) => {
  try {
    const uid = await verify(req);
    const { bookingId } = req.params;
    const { text } = req.body;
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      const [crow] = await conn.query('SELECT id, user_one_id, user_two_id FROM chats WHERE booking_id = ? LIMIT 1', [bookingId]);
      if (!crow.length) { await conn.rollback(); return res.status(404).json({ error: 'not_found' }); }
      const c = crow[0];
      if (![c.user_one_id, c.user_two_id].includes(Number(uid))) { await conn.rollback(); return res.status(403).json({ error: 'forbidden' }); }
      const now = new Date();
      const [ins] = await conn.query('INSERT INTO chat_messages (chat_id, sender_id, text, created_at) VALUES (?, ?, ?, ?)', [c.id, uid, text || '', now]);
      await conn.query('UPDATE chats SET last_message_at = ?, last_message_by = ?, last_message_text = ?, updated_at = NOW() WHERE id = ?', [now, uid, text || '', c.id]);
      await conn.commit();
      res.json({ id: ins.insertId });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally { conn.release(); }
  } catch (e) { res.status(401).json({ error: e.message }); }
});

async function getUserTokens(uids) {
  const db = admin.firestore();
  const tokens = [];
  for (const uid of uids) {
    try {
      const doc = await db.collection('users').doc(uid).get();
      if (doc.exists) {
        const d = doc.data();
        if (d.fcmToken) tokens.push(d.fcmToken);
        if (Array.isArray(d.fcmTokens)) tokens.push(...d.fcmTokens);
      }
    } catch (_) {}
  }
  return tokens.filter(Boolean);
}

app.post('/api/dev/bookings', async (req, res) => {
  try {
    if (process.env.FUNCTIONS_EMULATOR !== 'true') return res.status(400).json({ error: 'not_dev' });
    const { providerId, scheduledDate, address, notes } = req.body || {};
    const conn = await pool.getConnection();
    try {
      await conn.beginTransaction();
      const pidNum = Number(providerId);
      const pid = providerId && Number.isFinite(pidNum) && pidNum > 0 ? pidNum : null;
      const sched = scheduledDate ? new Date(scheduledDate) : new Date();
      const [result] = await conn.execute(
        'INSERT INTO bookings (client_id, provider_id, service_id, status, scheduled_at, address, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
        [null, pid, null, 'pending', sched, address || null, notes || null]
      );
      await conn.commit();
      res.json({ id: result.insertId.toString(), status: 'pending' });
    } catch (e) {
      await conn.rollback();
      res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
  } catch (e) {
    res.status(500).json({ error: e.message });
  }
});

app.post('/api/dev/seed', async (req, res) => {
  try {
    if (process.env.FUNCTIONS_EMULATOR !== 'true') return res.status(400).json({ error: 'not_dev' });
    const conn = await pool.getConnection();
    const created = { providers: [], firestore: { categories: [], services: [], providers: [] } };
    try {
      await conn.beginTransaction();
      try {
        await conn.execute("CREATE TABLE IF NOT EXISTS services (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255), category VARCHAR(64), price DECIMAL(10,2), updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)");
      } catch (_) {}
      const p1 = await conn.execute("INSERT INTO providers (name,email,verified,status) VALUES ('Alpha Provider','alpha@example.com',1,'active')");
      const p2 = await conn.execute("INSERT INTO providers (name,email,verified,status) VALUES ('Beta Provider','beta@example.com',1,'active')");
      const p3 = await conn.execute("INSERT INTO providers (name,email,verified,status) VALUES ('Gamma Provider','gamma@example.com',1,'active')");
      try {
        await conn.execute("INSERT INTO services (title,category,price) VALUES ('General Cleaning','cleaning',150)");
        await conn.execute("INSERT INTO services (title,category,price) VALUES ('Pipe Repair','plumbing',300)");
        await conn.execute("INSERT INTO services (title,category,price) VALUES ('Outlet Installation','electrical',500)");
      } catch (_) {}
      const [rows] = await conn.execute('SELECT id,name FROM providers ORDER BY id DESC LIMIT 3');
      created.providers = rows.map(r => ({ id: String(r.id), name: r.name }));
      await conn.commit();
    } catch (e) {
      await conn.rollback();
      return res.status(500).json({ error: e.message });
    } finally {
      conn.release();
    }
    try {
      const db = admin.firestore();
      const cats = [
      { id: 'cleaning', title: 'Cleaning' },
      { id: 'plumbing', title: 'Plumbing' },
      { id: 'electrical', title: 'Electrical' }
    ];
      for (const c of cats) {
        await db.collection('categories').doc(c.id).set({ slug: c.id, title: c.title });
        created.firestore.categories.push(c.id);
      }
      const svcs = [
      { id: 'general_cleaning', title: 'General Cleaning', category: 'cleaning', price: 150 },
      { id: 'pipe_repair', title: 'Pipe Repair', category: 'plumbing', price: 300 },
      { id: 'outlet_install', title: 'Outlet Installation', category: 'electrical', price: 500 }
    ];
      for (const s of svcs) {
        await db.collection('services').doc(s.id).set({ slug: s.id, title: s.title, category: s.category, price: s.price });
        created.firestore.services.push(s.id);
      }
      for (const p of created.providers) {
        await db.collection('providers').doc(p.id).set({ providerId: p.id, name: p.name, rating: 4.8, status: 'active' });
        created.firestore.providers.push(p.id);
      }
      await db.collection('users').doc('client1').set({ uid: 'client1', role: 'client' }, { merge: true });
      for (const p of created.providers) {
        await db.collection('users').doc(p.id).set({ uid: p.id, role: 'provider' }, { merge: true });
      }
    } catch (_) {}
    res.json(created);
  } catch (e) {
    res.status(500).json({ error: e.message });
  }
});

async function sendMulticast(title, body, data, uids) {
  return;
}

async function notifyBookingStatus(b, status) { return; }
async function notifyChatMessage(chat, senderId, text) { return; }

// Catch-all for other API routes
app.all('/api/*', (req, res) => {
  res.json({
    message: 'HausTap API is running!',
    endpoint: req.path,
    method: req.method,
    timestamp: new Date().toISOString(),
    availableEndpoints: [
      'GET /api/health',
      'GET /api/test-db',
      'GET /api/services',
      'GET /api/users',
      'GET /api/bookings'
    ]
  });
});

// Root endpoint
app.get('/', (req, res) => {
  res.json({
    message: 'HausTap API Server is Running!',
    version: '1.0.0',
    timestamp: new Date().toISOString(),
    database: 'MySQL',
    deployment: 'Firebase Functions',
    endpoints: [
      'GET /api/health',
      'GET /api/test-db',
      'GET /api/services',
      'GET /api/users',
      'GET /api/bookings'
    ]
  });
});

// Export the Express app as a Firebase Function
exports.api = functions
  .region('us-central1')
  .runWith({
    memory: '512MB',
    timeoutSeconds: 60,
    minInstances: 0,
    maxInstances: 10
  })
  .https.onRequest(app);
