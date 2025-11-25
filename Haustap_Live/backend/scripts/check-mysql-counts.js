const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../.env') });
const { connectDatabase, getPool } = require('../config/database');

async function count(table) {
  const pool = getPool();
  const [rows] = await pool.query(`SELECT COUNT(*) AS c FROM \`${table}\``);
  return rows[0]?.c || 0;
}

async function main() {
  await connectDatabase();
  const tables = ['users','services','service_subcategories','providers','bookings','chats','chat_messages'];
  const results = {};
  for (const t of tables) {
    try { results[t] = await count(t); } catch (e) { results[t] = `error: ${e.message}`; }
  }
  console.log(JSON.stringify(results, null, 2));
  process.exit(0);
}

main().catch(err => { console.error(err); process.exit(1); });
