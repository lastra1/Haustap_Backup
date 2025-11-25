// Test script to verify mobile app API configuration
const axios = require('axios');

async function testAPIs() {
  console.log('🧪 Testing HausTap Mobile API Configuration...\n');
  
  const apis = [
    {
      name: 'Local Laravel API',
      url: 'http://localhost:8001/api/health',
      description: 'Local Laravel backend'
    },
    {
      name: 'Local Node.js Server', 
      url: 'http://localhost:3000/api/send-otp',
      method: 'POST',
      data: { email: 'test@example.com', otp: '123456' },
      description: 'Local Node.js OTP server'
    },
    {
      name: 'Firebase Functions API',
      url: 'https://us-central1-haustap-booking-system.cloudfunctions.net/api/health',
      description: 'Live Firebase Functions'
    }
  ];
  
  for (const api of apis) {
    try {
      console.log(`Testing ${api.name}...`);
      console.log(`URL: ${api.url}`);
      
      const config = {
        method: api.method || 'GET',
        url: api.url,
        timeout: 5000,
        headers: { 'Content-Type': 'application/json' }
      };
      
      if (api.data) {
        config.data = api.data;
      }
      
      const response = await axios(config);
      console.log(`✅ ${api.name}: ONLINE`);
      console.log(`Status: ${response.status}`);
      console.log(`Response: ${JSON.stringify(response.data).substring(0, 100)}...`);
      
    } catch (error) {
      console.log(`❌ ${api.name}: ${error.message}`);
      if (error.code === 'ECONNREFUSED') {
        console.log('   Server not running or port blocked');
      } else if (error.code === 'ENOTFOUND') {
        console.log('   URL not found or DNS issue');
      }
    }
    
    console.log('---\n');
  }
  
  console.log('📱 Mobile App Configuration:');
  console.log('✅ API URL: https://us-central1-haustap-booking-system.cloudfunctions.net/api');
  console.log('✅ Local Alternative: http://localhost:8001');
  console.log('✅ OTP Server: http://localhost:3000');
  console.log('\n🚀 Ready for mobile app testing!');
}

testAPIs().catch(console.error);