import { createTransport } from 'nodemailer';

// Configure email transporter
const transporter = createTransport({
    service: 'gmail',
    auth: {
        user: process.env.GMAIL_USER,
        pass: process.env.GMAIL_APP_PASSWORD
    },
    tls: {
        rejectUnauthorized: false
    }
});

export const sendOtpEmail = async (email: string, otp: string) => {
    if (!process.env.GMAIL_USER || !process.env.GMAIL_APP_PASSWORD) {
        console.error('Missing email configuration');
        throw new Error('Email service not configured properly');
    }

    try {
        console.log('Attempting to send email to:', email);
        const mailOptions = {
            from: process.env.GMAIL_USER,
            to: email,
            subject: 'HausTap Email Verification',
            html: `
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #333;">Email Verification</h2>
                    <p>Thank you for signing up with HausTap. Please use the following OTP to verify your email address:</p>
                    <div style="background-color: #f4f4f4; padding: 15px; text-align: center; margin: 20px 0;">
                        <h1 style="color: #3DC1C6; letter-spacing: 5px; margin: 0;">${otp}</h1>
                    </div>
                    <p>This code will expire in 5 minutes.</p>
                    <p style="color: #666; font-size: 14px;">If you didn't request this verification, please ignore this email.</p>
                </div>
            `
        };

        const result = await transporter.sendMail(mailOptions);
        console.log('Email sent successfully:', result.messageId);
        return true;
    } catch (error) {
        console.error('Error sending email:', error);
        if (error instanceof Error) {
            console.error('Error details:', error.message);
            if ('code' in error) {
                console.error('Error code:', (error as any).code);
            }
        }
        throw error;
    }
};