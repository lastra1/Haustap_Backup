const express = require('express');
const router = express.Router();
const { getPool } = require('../config/database');

router.get('/health', (req, res) => {
  res.json({ status: 'OK', service: 'users' });
});

router.get('/', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query('SELECT id, name, email, role, status, phone, address FROM users ORDER BY id DESC');
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

router.get('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query('SELECT id, name, email, role, status, phone, address FROM users WHERE id = ?', [req.params.id]);
    if (!rows.length) return res.status(404).json({ error: 'Not found' });
    res.json(rows[0]);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

router.post('/', async (req, res) => {
  try {
    const { name, email, password, role, status, phone, address } = req.body;
    if (!email || !password) return res.status(400).json({ error: 'email and password required' });
    const pool = getPool();
    const [result] = await pool.query(
      'INSERT INTO users (name, email, password, role, status, phone, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
      [name || null, email, password, role || null, status || 'active', phone || null, address || null]
    );
    res.json({ id: result.insertId });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

router.put('/:id', async (req, res) => {
  try {
    const { name, role, status, phone, address } = req.body;
    const pool = getPool();
    const [result] = await pool.query(
      'UPDATE users SET name = ?, role = ?, status = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?',
      [name || null, role || null, status || null, phone || null, address || null, req.params.id]
    );
    res.json({ affected: result.affectedRows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

router.delete('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [result] = await pool.query('DELETE FROM users WHERE id = ?', [req.params.id]);
    res.json({ affected: result.affectedRows });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
