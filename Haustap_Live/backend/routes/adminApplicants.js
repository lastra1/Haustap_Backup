const express = require('express');
const router = express.Router();
const { getPool } = require('../config/database');

router.get('/health', (req, res) => res.json({ status: 'OK', service: 'admin_applicants' }));

router.get('/', async (req, res) => {
  try {
    const pool = getPool();
    const status = (req.query.status || '').toString().trim().toLowerCase();
    const q = (req.query.search || '').toString().trim().toLowerCase();
    const clauses = [];
    const params = [];
    if (status && status !== 'all') { clauses.push('LOWER(REPLACE(status, " ", "_")) = ?'); params.push(status); }
    if (q) { clauses.push('(LOWER(name) LIKE ? OR LOWER(email) LIKE ? OR LOWER(phone) LIKE ?)'); params.push(`%${q}%`, `%${q}%`, `%${q}%`); }
    const where = clauses.length ? ('WHERE ' + clauses.join(' AND ')) : '';
    const [rows] = await getPool().query(`SELECT id, name, email, phone, applied_at, status FROM admin_applicants ${where} ORDER BY id DESC`, params);
    res.json(rows);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.post('/', async (req, res) => {
  try {
    const { name, email, phone, applied_at, status } = req.body;
    if (!name) return res.status(400).json({ error: 'name required' });
    const [r] = await getPool().query(
      'INSERT INTO admin_applicants (name, email, phone, applied_at, status) VALUES (?, ?, ?, ?, ?)',
      [name, email || null, phone || null, applied_at || null, status || 'pending_review']
    );
    res.json({ id: r.insertId });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.put('/:id', async (req, res) => {
  try {
    const { name, email, phone, applied_at, status } = req.body;
    const [r] = await getPool().query(
      'UPDATE admin_applicants SET name=?, email=?, phone=?, applied_at=?, status=? WHERE id=?',
      [name || null, email || null, phone || null, applied_at || null, status || null, req.params.id]
    );
    res.json({ affected: r.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

router.delete('/:id', async (req, res) => {
  try {
    const [r] = await getPool().query('DELETE FROM admin_applicants WHERE id=?', [req.params.id]);
    res.json({ affected: r.affectedRows });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

module.exports = router;
