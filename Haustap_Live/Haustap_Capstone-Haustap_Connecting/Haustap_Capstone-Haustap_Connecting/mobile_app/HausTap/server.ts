import cors from 'cors';
import dotenv from 'dotenv';
import express from 'express';
import { sendOtpEmail } from './src/services';

// Load environment variables before anything else
dotenv.config();

// Verify required environment variables
if (!process.env.GMAIL_USER || !process.env.GMAIL_APP_PASSWORD) {
    console.error('Missing required environment variables: GMAIL_USER and/or GMAIL_APP_PASSWORD');
    process.exit(1);
}

const app = express();

// Middleware
app.use(cors({
  origin: '*', // Allow all origins in development
  credentials: true,
  methods: ['GET', 'POST'],
  allowedHeaders: ['Content-Type', 'Accept'],
}));
app.use(express.json());

// Error handling middleware
app.use((err: any, req: any, res: any, next: any) => {
  console.error('Error:', err);
  res.status(500).json({ 
    success: false, 
    message: 'Server error',
    error: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
});

const PORT = process.env.PORT || 3000;

app.post('/api/send-otp', async (req, res) => {
    try {
        const { email, otp } = req.body;
        const sent = await sendOtpEmail(email, otp);
        
        if (sent) {
            res.json({ success: true });
        } else {
            res.status(500).json({ success: false, message: 'Failed to send email' });
        }
    } catch (error) {
        console.error('Error in send-otp endpoint:', error);
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

app.listen(Number(PORT), '0.0.0.0', () => {
    console.log(`Server running on http://0.0.0.0:${PORT}`);
    console.log(`Email configured for: ${process.env.GMAIL_USER}`);
    console.log('Available on your network at:');
    console.log(`http://192.168.1.8:${PORT}`);
});