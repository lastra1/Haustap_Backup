const express = require('express');
const router = express.Router();
const { getPool } = require('../config/database');

router.get('/health', (req, res) => {
  res.json({ status: 'OK', service: 'services' });
});

router.get('/', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query('SELECT id, name, category, price, duration_minutes, description FROM services ORDER BY id DESC');
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

router.get('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query('SELECT id, name, category, price, duration_minutes, description FROM services WHERE id = ?', [req.params.id]);
    if (!rows.length) return res.status(404).json({ error: 'Not found' });
    res.json(rows[0]);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.post('/', async (req, res) => {
  try {
    const { name, category, price, duration_minutes, description } = req.body;
    if (!name) return res.status(400).json({ error: 'name required' });
    const pool = getPool();
    const [result] = await pool.query(
      'INSERT INTO services (name, category, price, duration_minutes, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
      [name, category || null, price || 0, duration_minutes || null, description || null]
    );
    res.json({ id: result.insertId });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.put('/:id', async (req, res) => {
  try {
    const { name, category, price, duration_minutes, description } = req.body;
    const pool = getPool();
    const [result] = await pool.query(
      'UPDATE services SET name=?, category=?, price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?',
      [name || null, category || null, price || 0, duration_minutes || null, description || null, req.params.id]
    );
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.delete('/:id', async (req, res) => {
  try {
    const pool = getPool();
    const [result] = await pool.query('DELETE FROM services WHERE id = ?', [req.params.id]);
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

module.exports = router;

router.get('/:id/subcategories', async (req, res) => {
  try {
    const pool = getPool();
    const [rows] = await pool.query('SELECT id, service_id, name, price, duration_minutes, description FROM service_subcategories WHERE service_id = ? ORDER BY id DESC', [req.params.id]);
    res.json(rows);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.post('/:id/subcategories', async (req, res) => {
  try {
    const { name, price, duration_minutes, description } = req.body;
    if (!name) return res.status(400).json({ error: 'name required' });
    const pool = getPool();
    const [result] = await pool.query(
      'INSERT INTO service_subcategories (service_id, name, price, duration_minutes, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
      [req.params.id, name, price || 0, duration_minutes || null, description || null]
    );
    res.json({ id: result.insertId });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.put('/subcategories/:subId', async (req, res) => {
  try {
    const { name, price, duration_minutes, description } = req.body;
    const pool = getPool();
    const [result] = await pool.query(
      'UPDATE service_subcategories SET name=?, price=?, duration_minutes=?, description=?, updated_at=NOW() WHERE id=?',
      [name || null, price || 0, duration_minutes || null, description || null, req.params.subId]
    );
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.delete('/subcategories/:subId', async (req, res) => {
  try {
    const pool = getPool();
    const [result] = await pool.query('DELETE FROM service_subcategories WHERE id = ?', [req.params.subId]);
    res.json({ affected: result.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});
