const path = require('path');
const fs = require('fs');
require('dotenv').config({ path: path.resolve(__dirname, '../.env') });

const { connectDatabase, getPool } = require('../config/database');

async function ensureTables(pool) {
  await pool.query(`CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL,
    role VARCHAR(50) NULL,
    status VARCHAR(50) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    category VARCHAR(100) NULL,
    price DECIMAL(10,2) DEFAULT 0,
    duration_minutes INT NULL,
    description TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS service_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    duration_minutes INT NULL,
    description TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    UNIQUE KEY uniq_service_name (service_id, name),
    INDEX idx_service (service_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    experience VARCHAR(100) NULL,
    verified TINYINT(1) DEFAULT 0,
    status VARCHAR(50) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NULL,
    provider_id INT NULL,
    service_id INT NULL,
    status VARCHAR(50) NULL,
    scheduled_at DATETIME NULL,
    address TEXT NULL,
    notes TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_client_status (client_id, status),
    INDEX idx_provider_status (provider_id, status)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_one_id INT NULL,
    user_two_id INT NULL,
    last_message_at DATETIME NULL,
    last_message_by VARCHAR(255) NULL,
    last_message_text TEXT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    INDEX idx_booking (booking_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  await pool.query(`CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender_id INT NULL,
    text TEXT NULL,
    created_at DATETIME NULL,
    INDEX idx_chat (chat_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);
}

function readJson(relPath) {
  const full = path.resolve(__dirname, `../api/storage/data/${relPath}`);
  if (!fs.existsSync(full)) return null;
  const raw = fs.readFileSync(full, 'utf8');
  try { return JSON.parse(raw); } catch { return null; }
}

function findTsDataFiles() {
  const roots = [
    path.resolve(__dirname, '../../../..', 'Haustap_Live', 'android-capstone-main', 'HausTap', 'app', 'client-side', 'data'),
    path.resolve(__dirname, '../../../..', 'Haustap_Application_tmp', 'HausTap', 'app', 'client-side', 'data'),
    path.resolve(__dirname, '../../../..', 'Haustap_Live', 'Haustap_Capstone-Haustap_Connecting', 'Haustap_Capstone-Haustap_Connecting', 'mobile_app', 'HausTap', 'app', 'client-side', 'data')
  ];
  const out = [];
  for (const r of roots) {
    if (!fs.existsSync(r)) continue;
    for (const f of fs.readdirSync(r)) {
      if (f.endsWith('.ts')) out.push(path.join(r, f));
    }
  }
  return out;
}

function parseTsPrice(p) {
  if (!p) return 0;
  const m = String(p).replace(/[^0-9.]/g, '');
  const n = parseFloat(m);
  return Number.isFinite(n) ? n : 0;
}

function normalizeServiceNameFromFile(file) {
  const b = path.basename(file).toLowerCase();
  if (b.includes('garden')) return 'Gardening';
  if (b.includes('clean')) return 'Cleaning';
  if (b.includes('laundry')) return 'Laundry';
  if (b.includes('hair')) return 'Hair';
  return 'General';
}

function extractSubcategoriesFromTs(file) {
  try {
    const src = fs.readFileSync(file, 'utf8');
    const arrs = [];
    const re = /\[([\s\S]*?)\]/g;
    let m;
    while ((m = re.exec(src))) arrs.push(m[1]);
    const items = [];
    for (const a of arrs) {
      const reItem = /\{[\s\S]*?title:\s*"([^"]+)"[\s\S]*?price:\s*"([^"]+)"[\s\S]*?\}/g;
      let mi;
      while ((mi = reItem.exec(a))) {
        items.push({ title: mi[1], price: parseTsPrice(mi[2]) });
      }
    }
    return items;
  } catch { return []; }
}

function findPhpFiles() {
  const roots = [
    path.resolve(__dirname, '../../../..', 'Haustap_Live', 'Haustap_Capstone-Haustap_Connecting', 'Haustap_Capstone-Haustap_Connecting', 'Outdoor_Services'),
    path.resolve(__dirname, '../../../..', 'Haustap_Live', 'Haustap_Capstone-Haustap_Connecting', 'Homecleaning')
  ];
  const out = [];
  for (const r of roots) {
    if (!fs.existsSync(r)) continue;
    const stack = [r];
    while (stack.length) {
      const dir = stack.pop();
      for (const f of fs.readdirSync(dir)) {
        const p = path.join(dir, f);
        const stat = fs.statSync(p);
        if (stat.isDirectory()) stack.push(p);
        else if (p.toLowerCase().endsWith('.php')) out.push(p);
      }
    }
  }
  return out;
}

function normalizeServiceNameFromPhpPath(file) {
  const low = file.toLowerCase();
  if (low.includes('outdoor_services') && low.includes('garden')) return 'Gardening';
  if (low.includes('homecleaning') || low.includes('clean')) return 'Cleaning';
  return 'General';
}

function extractSubcategoriesFromPhp(file) {
  try {
    const src = fs.readFileSync(file, 'utf8');
    const items = [];
    const priceRe = /class\s*=\s*"[^"]*service-price[^"]*"[^>]*>([^<]+)<\/[^>]+/gi;
    let m;
    const lines = src.split(/\r?\n/);
    const text = src;
    while ((m = priceRe.exec(text))) {
      const priceText = m[1];
      const price = parseTsPrice(priceText);
      const idx = m.index;
      const before = text.slice(Math.max(0, idx - 1000), idx);
      const titleMatch = before.match(/<[^>]*class\s*=\s*"[^"]*(service-title|service-name)[^"]*"[^>]*>([^<]+)<\/[^>]+/i)
        || before.match(/<h[23][^>]*>([^<]+)<\/h[23]>/i);
      const title = titleMatch ? (titleMatch[2] || titleMatch[1]) : 'Service Item';
      items.push({ title: title.trim(), price });
    }
    return items;
  } catch { return []; }
}

async function tableColumns(pool, table) {
  const [rows] = await pool.query(`SHOW COLUMNS FROM \`${table}\``);
  return rows.map(r => r.Field);
}

async function upsertUsers(pool) {
  const data = readJson('users.json');
  if (!data || !Array.isArray(data.users)) return {};
  const cols = await tableColumns(pool, 'users');
  const emailToId = {};
  for (const u of data.users) {
    const name = u.name || null;
    const email = u.email;
    const password = u.password_hash || null;
    const role = u.role || 'client';
    const status = 'active';
    const [existing] = await pool.query('SELECT id FROM users WHERE email = ?', [email]);
    let id;
    if (existing.length) {
      id = existing[0].id;
      const setParts = [];
      const params = [];
      if (cols.includes('name')) { setParts.push('name=?'); params.push(name); }
      if (cols.includes('password')) { setParts.push('password=?'); params.push(password); }
      if (cols.includes('role')) { setParts.push('role=?'); params.push(role); }
      if (cols.includes('status')) { setParts.push('status=?'); params.push(status); }
      setParts.push('updated_at=NOW()');
      params.push(id);
      await pool.query(`UPDATE users SET ${setParts.join(', ')} WHERE id=?`, params);
    } else {
      const insertCols = ['email'];
      const placeholders = ['?'];
      const values = [email];
      if (cols.includes('name')) { insertCols.push('name'); placeholders.push('?'); values.push(name); }
      if (cols.includes('password')) { insertCols.push('password'); placeholders.push('?'); values.push(password); }
      if (cols.includes('role')) { insertCols.push('role'); placeholders.push('?'); values.push(role); }
      if (cols.includes('status')) { insertCols.push('status'); placeholders.push('?'); values.push(status); }
      if (cols.includes('created_at')) { insertCols.push('created_at'); placeholders.push('NOW()'); }
      if (cols.includes('updated_at')) { insertCols.push('updated_at'); placeholders.push('NOW()'); }
      const [res] = await pool.query(`INSERT INTO users (${insertCols.join(',')}) VALUES (${placeholders.join(',')})`, values);
      id = res.insertId;
    }
    emailToId[email] = id;
  }
  return emailToId;
}

async function upsertServices(pool) {
  const defaultServices = [
    { name: 'Cleaning Services', category: 'Cleaning', price: 0, duration_minutes: 0, description: 'Top-level category for cleaning' },
    { name: 'Outdoor Services', category: 'Outdoor', price: 0, duration_minutes: 0, description: 'Top-level category for outdoor' },
    { name: 'Home Repairs', category: 'Repairs', price: 0, duration_minutes: 0, description: 'Top-level category for home repairs' },
    { name: 'Beauty Services', category: 'Beauty', price: 0, duration_minutes: 0, description: 'Top-level category for beauty' },
    { name: 'Wellness Services', category: 'Wellness', price: 0, duration_minutes: 0, description: 'Top-level category for wellness' },
    { name: 'Tech & Gadget Services', category: 'Tech', price: 0, duration_minutes: 0, description: 'Top-level category for tech & gadgets' },
    // Legacy base services for compatibility
    { name: 'Cleaning', category: 'Home', price: 500, duration_minutes: 60, description: 'General home cleaning' },
    { name: 'Gardening', category: 'Outdoor', price: 700, duration_minutes: 90, description: 'Garden maintenance' },
    { name: 'Laundry', category: 'Home', price: 300, duration_minutes: 45, description: 'Laundry and folding' }
  ];
  const nameToId = {};
  for (const s of defaultServices) {
    const [existing] = await pool.query('SELECT id FROM services WHERE name = ?', [s.name]);
    let id;
    if (existing.length) {
      id = existing[0].id;
      await pool.query('UPDATE services SET category=?, price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?', [s.category, s.price, s.duration_minutes, s.description, id]);
    } else {
      const [res] = await pool.query('INSERT INTO services (name,category,price,duration_minutes,description,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())', [s.name, s.category, s.price, s.duration_minutes, s.description]);
      id = res.insertId;
    }
    nameToId[s.name] = id;
  }
  return nameToId;
}

async function upsertSubcategories(pool, serviceNameToId) {
  const subcats = [
    // Cleaning Services
    { service: 'Cleaning Services', name: 'Basic Cleaning', price: 1000, duration_minutes: 180, description: 'Basic – 1 cleaner' },
    { service: 'Cleaning Services', name: 'Standard Cleaning', price: 2000, duration_minutes: 240, description: 'Standard – 2 cleaners' },
    { service: 'Cleaning Services', name: 'Deep Cleaning', price: 3000, duration_minutes: 300, description: 'Deep – 3 cleaners' },
    // Outdoor Services
    { service: 'Outdoor Services', name: 'Gardening - Basic (1 gardener)', price: 500, duration_minutes: 120, description: 'Grass cutting / trimming' },
    { service: 'Outdoor Services', name: 'Gardening - Standard (2 gardeners)', price: 1000, duration_minutes: 180, description: 'Expanded tasks' },
    { service: 'Outdoor Services', name: 'Tree Trimming', price: 1200, duration_minutes: 180, description: 'Small to medium trees' },
    { service: 'Outdoor Services', name: 'Yard Cleanup', price: 900, duration_minutes: 150, description: 'Debris removal' },
    // Home Repairs
    { service: 'Home Repairs', name: 'Electrical Repair - Basic Fix', price: 800, duration_minutes: 90, description: 'Outlet/switch repair' },
    { service: 'Home Repairs', name: 'Plumbing - Leak Fix', price: 900, duration_minutes: 90, description: 'Leak detection/repair' },
    { service: 'Home Repairs', name: 'Appliance Repair - Diagnosis', price: 700, duration_minutes: 60, description: 'Diagnostic and basic repair' },
    { service: 'Home Repairs', name: 'Handyman - Minor Repairs', price: 600, duration_minutes: 60, description: 'General minor repairs' },
    // Beauty Services
    { service: 'Beauty Services', name: 'Lash Removal', price: 500, duration_minutes: 60, description: 'Safe removal' },
    { service: 'Beauty Services', name: 'Lash Retouch / Refill (2–3 weeks)', price: 800, duration_minutes: 90, description: 'Fills in gaps' },
    { service: 'Beauty Services', name: 'Makeup - Natural Day Look', price: 700, duration_minutes: 90, description: 'Light foundation' },
    { service: 'Beauty Services', name: 'Makeup - Evening/Party', price: 1200, duration_minutes: 120, description: 'Bold eyes' },
    { service: 'Beauty Services', name: 'Makeup - Bridal (Trial + Wedding Day)', price: 5000, duration_minutes: 300, description: 'Trial + wedding day' },
    { service: 'Beauty Services', name: 'Manicure', price: 250, duration_minutes: 45, description: 'Standard manicure' },
    { service: 'Beauty Services', name: 'Pedicure', price: 300, duration_minutes: 50, description: 'Standard pedicure' },
    { service: 'Beauty Services', name: 'Gel Manicure', price: 700, duration_minutes: 60, description: 'Gel manicure' },
    { service: 'Beauty Services', name: 'Gel Pedicure', price: 800, duration_minutes: 70, description: 'Gel pedicure' },
    // Wellness Services
    { service: 'Wellness Services', name: 'Home Massage - 60 mins', price: 800, duration_minutes: 60, description: 'Relaxation massage' },
    { service: 'Wellness Services', name: 'Home Massage - 90 mins', price: 1100, duration_minutes: 90, description: 'Extended massage' },
    { service: 'Wellness Services', name: 'Yoga Session - 60 mins', price: 600, duration_minutes: 60, description: 'Guided yoga session' },
    { service: 'Wellness Services', name: 'Spa Facial - Basic', price: 900, duration_minutes: 75, description: 'Cleansing, exfoliation' },
    // Tech & Gadget Services
    { service: 'Tech & Gadget Services', name: 'Laptop - Fan / Cooling Repair', price: 500, duration_minutes: 60, description: 'Cooling system repair' },
    { service: 'Tech & Gadget Services', name: 'Laptop - Keyboard Replacement', price: 500, duration_minutes: 60, description: 'Keyboard replacement' },
    { service: 'Tech & Gadget Services', name: 'Laptop - OS Reformat + Software Installation', price: 700, duration_minutes: 90, description: 'OS reinstall + apps' },
    { service: 'Tech & Gadget Services', name: 'Desktop PC - Fan / Cooling Repair', price: 500, duration_minutes: 60, description: 'Cooling system repair' },
    { service: 'Tech & Gadget Services', name: 'Desktop PC - Keyboard Replacement', price: 500, duration_minutes: 60, description: 'Keyboard replacement' },
    { service: 'Tech & Gadget Services', name: 'Desktop PC - OS Reformat + Software Installation', price: 700, duration_minutes: 90, description: 'OS reinstall + apps' },
    { service: 'Tech & Gadget Services', name: 'Mobile Phone - Screen Replacement', price: 1500, duration_minutes: 90, description: 'Screen replacement' },
    { service: 'Tech & Gadget Services', name: 'Tablet & iPad - OS Reinstall', price: 800, duration_minutes: 90, description: 'System reinstall' },
    { service: 'Tech & Gadget Services', name: 'Game & Console - System Update', price: 300, duration_minutes: 60, description: 'Firmware update' },
    // Legacy base service-specific subcats for compatibility
    { service: 'Cleaning', name: 'Deep Cleaning', price: 1200, duration_minutes: 120, description: 'Intensive cleaning' },
    { service: 'Cleaning', name: 'Move-out Cleaning', price: 1500, duration_minutes: 150, description: 'Post-tenancy cleaning' },
    { service: 'Gardening', name: 'Grass Cutting', price: 600, duration_minutes: 60, description: 'Lawn mowing' },
    { service: 'Gardening', name: 'Hedge Trimming', price: 800, duration_minutes: 90, description: 'Hedge maintenance' },
    { service: 'Laundry', name: 'Wash & Fold', price: 350, duration_minutes: 60, description: 'Standard wash and fold' },
    { service: 'Laundry', name: 'Steam Iron', price: 250, duration_minutes: 45, description: 'Steam ironing' }
  ];
  for (const sc of subcats) {
    const sid = serviceNameToId[sc.service];
    if (!sid) continue;
    const [existing] = await pool.query('SELECT id FROM service_subcategories WHERE service_id=? AND name=?', [sid, sc.name]);
    if (existing.length) {
      await pool.query('UPDATE service_subcategories SET price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?', [sc.price, sc.duration_minutes, sc.description, existing[0].id]);
    } else {
      await pool.query('INSERT INTO service_subcategories (service_id, name, price, duration_minutes, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())', [sid, sc.name, sc.price, sc.duration_minutes, sc.description]);
    }
  }

  const files = findTsDataFiles();
  for (const f of files) {
    const sname = normalizeServiceNameFromFile(f);
    const sid = serviceNameToId[sname];
    if (!sid) continue;
    const items = extractSubcategoriesFromTs(f);
    for (const it of items) {
      const [exists] = await pool.query('SELECT id FROM service_subcategories WHERE service_id=? AND name=?', [sid, it.title]);
      if (exists.length) {
        await pool.query('UPDATE service_subcategories SET price=?, updated_at=NOW() WHERE id=?', [it.price, exists[0].id]);
      } else {
        await pool.query('INSERT INTO service_subcategories (service_id, name, price, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', [sid, it.title, it.price]);
      }
    }
  }

  const catalog = readJson('services.catalog.json');
  if (catalog && Array.isArray(catalog.services)) {
    for (const svc of catalog.services) {
      const sid = serviceNameToId[svc.name] || null;
      if (!sid) continue;
      const subs = Array.isArray(svc.subcategories) ? svc.subcategories : [];
      for (const sc of subs) {
        const [exists] = await pool.query('SELECT id FROM service_subcategories WHERE service_id=? AND name=?', [sid, sc.name]);
        if (exists.length) {
          await pool.query('UPDATE service_subcategories SET price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?', [sc.price || 0, sc.duration_minutes || null, sc.description || null, exists[0].id]);
        } else {
          await pool.query('INSERT INTO service_subcategories (service_id, name, price, duration_minutes, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())', [sid, sc.name, sc.price || 0, sc.duration_minutes || null, sc.description || null]);
        }
      }
    }
  }

  const phpFiles = findPhpFiles();
  for (const f of phpFiles) {
    const sname = normalizeServiceNameFromPhpPath(f);
    const sid = serviceNameToId[sname];
    if (!sid) continue;
    const items = extractSubcategoriesFromPhp(f);
    for (const it of items) {
      const [exists] = await pool.query('SELECT id FROM service_subcategories WHERE service_id=? AND name=?', [sid, it.title]);
      if (exists.length) {
        await pool.query('UPDATE service_subcategories SET price=?, updated_at=NOW() WHERE id=?', [it.price, exists[0].id]);
      } else {
        await pool.query('INSERT INTO service_subcategories (service_id, name, price, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', [sid, it.title, it.price]);
      }
    }
  }
}

async function upsertProviders(pool) {
  const apps = readJson('provider_applications.json') || {};
  const statusMap = readJson('provider_status.json') || {};
  const cols = await tableColumns(pool, 'providers');
  const emailToId = {};
  for (const [email, app] of Object.entries(apps)) {
    const name = app.name || 'Provider';
    const phone = app.phone || null;
    const experience = app.experience || null;
    const status = (statusMap[email]?.status) || app.status || 'pending';
    const [existing] = await pool.query('SELECT id FROM providers WHERE email = ?', [email]);
    let id;
    if (existing.length) {
      id = existing[0].id;
      const setParts = [];
      const params = [];
      if (cols.includes('name')) { setParts.push('name=?'); params.push(name); }
      if (cols.includes('phone')) { setParts.push('phone=?'); params.push(phone); }
      if (cols.includes('experience')) { setParts.push('experience=?'); params.push(experience); }
      if (cols.includes('status')) { setParts.push('status=?'); params.push(status); }
      setParts.push('updated_at=NOW()');
      params.push(id);
      await pool.query(`UPDATE providers SET ${setParts.join(', ')} WHERE id=?`, params);
    } else {
      const insertCols = ['email'];
      const placeholders = ['?'];
      const values = [email];
      if (cols.includes('name')) { insertCols.push('name'); placeholders.push('?'); values.push(name); }
      if (cols.includes('phone')) { insertCols.push('phone'); placeholders.push('?'); values.push(phone); }
      if (cols.includes('experience')) { insertCols.push('experience'); placeholders.push('?'); values.push(experience); }
      if (cols.includes('status')) { insertCols.push('status'); placeholders.push('?'); values.push(status); }
      if (cols.includes('created_at')) { insertCols.push('created_at'); placeholders.push('NOW()'); }
      if (cols.includes('updated_at')) { insertCols.push('updated_at'); placeholders.push('NOW()'); }
      const [res] = await pool.query(`INSERT INTO providers (${insertCols.join(',')}) VALUES (${placeholders.join(',')})`, values);
      id = res.insertId;
    }
    emailToId[email] = id;
  }
  return emailToId;
}

async function upsertBookings(pool, serviceNameToId, emailToUserId, emailToProviderId) {
  const bookings = readJson('bookings.json') || [];
  const cols = await tableColumns(pool, 'bookings');
  for (const b of bookings) {
    const status = b.status || 'pending';
    const scheduledAt = (b.scheduled_date && b.scheduled_time) ? `${b.scheduled_date} ${b.scheduled_time}:00` : null;
    const address = b.address || null;
    const notes = b.notes || null;
    const serviceId = b.service_name ? (serviceNameToId[b.service_name] || null) : null;
    const providerId = b.provider_id ? b.provider_id : null;
    const insertCols = [];
    const placeholders = [];
    const values = [];
    if (cols.includes('client_id')) { insertCols.push('client_id'); placeholders.push('?'); values.push(null); }
    if (cols.includes('provider_id')) { insertCols.push('provider_id'); placeholders.push('?'); values.push(providerId); }
    if (cols.includes('service_id')) { insertCols.push('service_id'); placeholders.push('?'); values.push(serviceId); }
    if (cols.includes('status')) { insertCols.push('status'); placeholders.push('?'); values.push(status); }
    if (cols.includes('scheduled_at')) { insertCols.push('scheduled_at'); placeholders.push('?'); values.push(scheduledAt); }
    if (cols.includes('address')) { insertCols.push('address'); placeholders.push('?'); values.push(address); }
    if (cols.includes('notes')) { insertCols.push('notes'); placeholders.push('?'); values.push(notes); }
    if (cols.includes('created_at')) { insertCols.push('created_at'); placeholders.push('NOW()'); }
    if (cols.includes('updated_at')) { insertCols.push('updated_at'); placeholders.push('NOW()'); }
    if (!insertCols.length) continue;
    await pool.query(`INSERT INTO bookings (${insertCols.join(',')}) VALUES (${placeholders.join(',')})`, values);
  }
}

async function main() {
  await connectDatabase();
  const pool = getPool();
  if (!pool) throw new Error('Database pool not initialized');
  await ensureTables(pool);
  const emailToUserId = await upsertUsers(pool);
  const serviceNameToId = await upsertServices(pool);
  const emailToProviderId = await upsertProviders(pool);
  await upsertSubcategories(pool, serviceNameToId);
  await upsertBookings(pool, serviceNameToId, emailToUserId, emailToProviderId);
  console.log('Seeding completed');
  process.exit(0);
}

main().catch(err => { console.error(err); process.exit(1); });
