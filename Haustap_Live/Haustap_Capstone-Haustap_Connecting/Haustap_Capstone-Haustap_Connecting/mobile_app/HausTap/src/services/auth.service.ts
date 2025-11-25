
class AuthService {
  // Store sent OTPs temporarily (in a real app, this would be handled by the backend)
  private otpStore: Map<string, { otp: string; timestamp: number }> = new Map();
  
  // Generate a random 6-digit OTP
  private generateOTP(): string {
    return Math.floor(100000 + Math.random() * 900000).toString();
  }

  // Send OTP to email (simulated)
  async sendOTP(email: string): Promise<boolean> {
    try {
      // Generate new OTP
      const otp = this.generateOTP();
      
      // Store OTP with timestamp
      this.otpStore.set(email, {
        otp,
        timestamp: Date.now()
      });

      // Simulate API call delay
      await new Promise(resolve => setTimeout(resolve, 1000));

      // In a real app, you would call your backend API here
      console.log(`OTP for ${email}: ${otp}`); // For development testing only
      
      return true;
    } catch (error) {
      console.error('Error sending OTP:', error);
      return false;
    }
  }

  // Verify the OTP
  async verifyOTP(email: string, submittedOTP: string): Promise<boolean> {
    try {
      const storedData = this.otpStore.get(email);
      
      if (!storedData) {
        throw new Error('No OTP found for this email');
      }

      const { otp, timestamp } = storedData;
      const now = Date.now();
      const otpAge = now - timestamp;
      
      // OTP expires after 5 minutes
      if (otpAge > 5 * 60 * 1000) {
        this.otpStore.delete(email);
        throw new Error('OTP has expired');
      }

      // Verify OTP
      if (submittedOTP === otp) {
        // Clear the used OTP
        this.otpStore.delete(email);
        return true;
      }

      return false;
    } catch (error) {
      console.error('Error verifying OTP:', error);
      return false;
    }
  }

  // Check if email has a valid OTP that can be resent
  canResendOTP(email: string): boolean {
    const storedData = this.otpStore.get(email);
    if (!storedData) return true;

    const now = Date.now();
    const timeSinceLastOTP = now - storedData.timestamp;
    
    // Allow resend after 30 seconds
    return timeSinceLastOTP > 30 * 1000;
  }
}

// Export singleton instance
export const authService = new AuthService();