const express = require('express');
const router = express.Router();

// Health check endpoint
router.get('/health', (req, res) => {
  res.json({ status: 'OK', service: 'auth' });
});

// Basic auth endpoints (mock implementation)
router.post('/login', (req, res) => {
  const { email, password } = req.body;
  
  if (!email || !password) {
    return res.status(400).json({ error: 'Email and password are required' });
  }
  
  // Mock login response
  res.json({
    message: 'Login successful',
    token: 'mock-jwt-token',
    user: {
      id: 1,
      email: email,
      name: 'Test User'
    }
  });
});

router.post('/register', (req, res) => {
  const { email, password, name } = req.body;
  
  if (!email || !password || !name) {
    return res.status(400).json({ error: 'Email, password, and name are required' });
  }
  
  // Mock registration response
  res.json({
    message: 'Registration successful',
    user: {
      id: 2,
      email: email,
      name: name
    }
  });
});

router.post('/logout', (req, res) => {
  res.json({ message: 'Logout successful' });
});

module.exports = router;