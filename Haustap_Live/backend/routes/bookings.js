const express = require('express');
const router = express.Router();
const { getPool } = require('../config/database');

router.get('/health', (req, res) => {
  res.json({ status: 'OK', service: 'bookings' });
});

router.get('/availability', async (req, res) => {
  try {
    const dateStr = (req.query.date || '').toString();
    if (!dateStr) { return res.status(400).json({ error: 'date is required (YYYY-MM-DD)' }); }
    const d = new Date(dateStr + 'T00:00:00');
    if (isNaN(d.getTime())) { return res.status(400).json({ error: 'invalid date' }); }
    const dow = d.getDay();
    const isWeekend = dow === 0 || dow === 6;
    if (isWeekend) { return res.json({ date: dateStr, available: false, slots: [] }); }
    const slots = [];
    for (let h = 8; h <= 20; h++) {
      for (let m = 0; m < 60; m += 30) {
        if (h === 20 && m > 0) break;
        const label = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate(), h, m)).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        slots.push(label);
      }
    }
    res.json({ date: dateStr, available: true, slots });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.get('/', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query(
      `SELECT b.id, b.status, b.scheduled_at, b.address, b.notes,
              u.name AS client_name, p.name AS provider_name, s.name AS service_name
       FROM bookings b
       LEFT JOIN users u ON u.id = b.client_id
       LEFT JOIN providers p ON p.id = b.provider_id
       LEFT JOIN services s ON s.id = b.service_id
       ORDER BY b.id DESC`
    );
    res.json(rows);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.get('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query(
      `SELECT b.id, b.status, b.scheduled_at, b.address, b.notes,
              u.name AS client_name, p.name AS provider_name, s.name AS service_name
       FROM bookings b
       LEFT JOIN users u ON u.id = b.client_id
       LEFT JOIN providers p ON p.id = b.provider_id
       LEFT JOIN services s ON s.id = b.service_id
       WHERE b.id = ?`,
      [req.params.id]
    );
    if (!rows.length) return res.status(404).json({ error: 'Not found' });
    res.json(rows[0]);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.post('/', async (req, res) => {
  try {
    const { client_id, provider_id, service_id, service_name, status, scheduled_at, address, notes } = req.body;
    const pool = getPool();
    let resolvedServiceId = service_id || null;
    if (!resolvedServiceId && service_name) {
      const [subRows] = await pool.query('SELECT id FROM service_subcategories WHERE name = ? LIMIT 1', [service_name]);
      if (subRows && subRows.length) {
        resolvedServiceId = subRows[0].id;
      } else {
        const [svcRows] = await pool.query('SELECT id FROM services WHERE name = ? LIMIT 1', [service_name]);
        if (svcRows && svcRows.length) {
          resolvedServiceId = svcRows[0].id;
        }
      }
    }
    const [result] = await pool.query(
      'INSERT INTO bookings (client_id, provider_id, service_id, status, scheduled_at, address, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
      [client_id || null, provider_id || null, resolvedServiceId || null, status || 'pending', scheduled_at || null, address || null, notes || null]
    );
    res.json({ id: result.insertId });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.put('/:id', async (req, res) => {
  try {
    const { status, scheduled_at, address, notes, provider_id } = req.body;
    const pool = getPool();
    const [result] = await pool.query(
      'UPDATE bookings SET status=?, scheduled_at=?, address=?, notes=?, provider_id=?, updated_at=NOW() WHERE id=?',
      [status || null, scheduled_at || null, address || null, notes || null, provider_id || null, req.params.id]
    );
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.delete('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [result] = await pool.query('DELETE FROM bookings WHERE id = ?', [req.params.id]);
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

module.exports = router;
