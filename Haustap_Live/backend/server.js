const express = require('express');
const http = require('http');
const path = require('path');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const compression = require('compression');
const rateLimit = require('express-rate-limit');
require('dotenv').config();

const { connectDatabase } = require('./config/database');
const { initializeFirebase } = require('./config/firebase');
const logger = require('./utils/logger');

// Import routes
const authRoutes = require('./routes/auth');
const userRoutes = require('./routes/users');
const serviceRoutes = require('./routes/services');
const bookingRoutes = require('./routes/bookings');
const adminApplicantsRoutes = require('./routes/adminApplicants');
const firebaseRoutes = require('./routes/firebase');
const clientRoutes = require('./routes/client');

const app = express();
const PORT = process.env.PORT || 10000;

// Security middleware
app.use(helmet());

// CORS configuration
const corsOptions = {
  origin: process.env.CORS_ORIGIN || '*',
  credentials: process.env.CORS_CREDENTIALS === 'true',
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
};
app.use(cors(corsOptions));

// Rate limiting
const limiter = rateLimit({
  windowMs: parseInt(process.env.RATE_LIMIT_WINDOW_MS) || 15 * 60 * 1000, // 15 minutes
  max: parseInt(process.env.RATE_LIMIT_MAX_REQUESTS) || 100,
  message: 'Too many requests from this IP, please try again later.',
  standardHeaders: true,
  legacyHeaders: false,
});
app.use('/api/', limiter);

// Body parsing middleware
app.use(compression());
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// Logging
app.use(morgan('combined', { stream: { write: message => logger.info(message.trim()) } }));

// Health check endpoint
app.get('/api/health', async (req, res) => {
  try {
    const health = {
      status: 'healthy',
      timestamp: new Date().toISOString(),
      uptime: process.uptime(),
      environment: process.env.NODE_ENV || 'development',
      services: {
        database: 'unknown',
        firebase: 'unknown'
      }
    };

    // Test database connection
    try {
      const { getPool } = require('./config/database');
      const pool = getPool();
      if (pool) {
        const connection = await pool.getConnection();
        await connection.ping();
        connection.release();
        health.services.database = 'healthy';
      } else {
        health.services.database = 'not_configured';
      }
    } catch (dbError) {
      health.services.database = 'unhealthy';
      logger.error('Database health check failed:', dbError);
    }

    // Test Firebase connection
    try {
      const { getFirestore } = require('./config/firebase');
      const db = getFirestore();
      health.services.firebase = 'healthy';
    } catch (firebaseError) {
      health.services.firebase = 'unhealthy';
      logger.error('Firebase health check failed:', firebaseError);
    }

    // Determine overall status
    const hasUnhealthyService = Object.values(health.services).includes('unhealthy');
    const hasUnknownService = Object.values(health.services).includes('unknown');
    
    if (hasUnhealthyService) {
      health.status = 'unhealthy';
      res.status(503);
    } else if (hasUnknownService) {
      health.status = 'degraded';
      res.status(200);
    } else {
      health.status = 'healthy';
      res.status(200);
    }

    res.json(health);
  } catch (error) {
    logger.error('Health check failed:', error);
    res.status(503).json({
      status: 'unhealthy',
      timestamp: new Date().toISOString(),
      error: error.message
    });
  }
});

// API routes
app.use('/api/auth', authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/services', serviceRoutes);
app.use('/api/bookings', bookingRoutes);
app.use('/api/admin/applicants', adminApplicantsRoutes);
app.use('/api/firebase', firebaseRoutes);
app.use('/client', clientRoutes);

function proxyPhp(pathname, res) {
  const options = { hostname: 'localhost', port: 8003, path: `/${pathname}`, method: 'GET' };
  const req = http.request(options, r => {
    res.status(r.statusCode || 200);
    const ct = r.headers['content-type'] || 'text/plain';
    res.set('Content-Type', ct);
    r.pipe(res);
  });
  req.on('error', () => res.status(502).send('Bad gateway'));
  req.end();
}

app.get('/css/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyPhp('css/' + tail, res);
});

app.get('/js/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyPhp('js/' + tail, res);
});

app.get('/images/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyPhp('images/' + tail, res);
});

app.get('/my_account/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyPhp('my_account/' + tail, res);
});

app.get('/guest/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyPhp('guest/' + tail, res);
});

app.get('/client/js/multi-select-services.js', (req, res) => {
  const file = path.join(__dirname, '..', 'client', 'js', 'multi-select-services.js');
  res.sendFile(file);
});

// Root endpoint
app.get('/', (req, res) => {
  res.redirect(302, '/client/gardening');
});

app.get('/client', (req, res) => {
  res.redirect(302, '/client/gardening');
});

// 404 handler
app.use('*', (req, res) => {
  res.status(404).json({
    error: 'Route not found',
    path: req.originalUrl,
    method: req.method
  });
});

// Error handling middleware
app.use((error, req, res, next) => {
  logger.error('Unhandled error:', error);
  
  // Don't leak error details in production
  const isDevelopment = process.env.NODE_ENV === 'development';
  
  res.status(error.status || 500).json({
    error: error.message || 'Internal server error',
    ...(isDevelopment && { stack: error.stack }),
    timestamp: new Date().toISOString()
  });
});

// Graceful shutdown
process.on('SIGTERM', () => {
  logger.info('SIGTERM received, shutting down gracefully');
  process.exit(0);
});

process.on('SIGINT', () => {
  logger.info('SIGINT received, shutting down gracefully');
  process.exit(0);
});

// Initialize services and start server
async function startServer() {
  try {
    logger.info('Starting HausTap API server...');
    
    // Initialize database
    await connectDatabase();
    logger.info('Database initialized');
    
    // Initialize Firebase
    await initializeFirebase();
    logger.info('Firebase initialized');
    
    // Start server
    app.listen(PORT, '0.0.0.0', () => {
      logger.info(`🚀 HausTap API server running on port ${PORT}`);
      logger.info(`📊 Health check: http://localhost:${PORT}/api/health`);
      logger.info(`📖 API Documentation: http://localhost:${PORT}/api/`);
    });
    
  } catch (error) {
    logger.error('Failed to start server:', error);
    process.exit(1);
  }
}

// Start the server
if (require.main === module) {
  startServer();
}

module.exports = app;
