const express = require('express');
const router = express.Router();

// Health check endpoint
router.get('/health', (req, res) => {
  res.json({ status: 'OK', service: 'firebase' });
});

// Basic Firebase endpoints (mock implementation)
router.get('/config', (req, res) => {
  // Mock Firebase configuration
  res.json({
    apiKey: 'mock-api-key',
    authDomain: 'haustap-booking-system.firebaseapp.com',
    projectId: 'haustap-booking-system',
    storageBucket: 'haustap-booking-system.appspot.com',
    messagingSenderId: '123456789',
    appId: 'mock-app-id'
  });
});

router.post('/verify-token', (req, res) => {
  const { token } = req.body;
  
  if (!token) {
    return res.status(400).json({ error: 'Token is required' });
  }
  
  // Mock token verification
  res.json({
    message: 'Token verified successfully',
    uid: 'mock-user-id',
    email: 'user@example.com'
  });
});

router.get('/categories', (req, res) => {
  res.json({
    success: true,
    categories: [
      { slug: 'cleaning', name: 'Cleaning Services', description: 'Home and commercial cleaning' },
      { slug: 'outdoor', name: 'Outdoor Services', description: 'Gardening and landscaping' },
      { slug: 'repairs', name: 'Home Repairs', description: 'Handyman and appliance repair' },
      { slug: 'beauty', name: 'Beauty Services', description: 'Hair, makeup, nails, lash' },
      { slug: 'wellness', name: 'Wellness Services', description: 'Massage and wellness' },
      { slug: 'tech', name: 'Tech & Gadget Services', description: 'Device setup and repair' }
    ]
  });
});

module.exports = router;
