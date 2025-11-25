const mysql = require('mysql2/promise');
const logger = require('../utils/logger');

let pool;

const connectDatabase = async () => {
  try {
    pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      port: process.env.DB_PORT || 3306,
      database: process.env.DB_NAME || 'haustap_db',
      user: process.env.DB_USER || 'haustap_user',
      password: process.env.DB_PASSWORD || 'haustap_password',
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0
    });

    // Test the connection
    const connection = await pool.getConnection();
    await connection.ping();
    connection.release();
    
    logger.info('MySQL database connected successfully');
  } catch (error) {
    logger.error('Failed to connect to MySQL database:', error);
    // Don't throw error, let the app continue without database
    logger.warn('Continuing without database connection');
  }
};

const getPool = () => pool;

module.exports = {
  connectDatabase,
  getPool
};