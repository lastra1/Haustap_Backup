import apiClient from "../../services/api-client";


interface OtpResponse {
    success: boolean;
    email_sent: boolean;
    expires: number
    otp: string;
}

export const sendEmailOtp = async (email: string): Promise<OtpResponse | null> => {
  try {
    const response = await apiClient.post('/api/auth/otp/send', {
      email: email
    });

    return response.data;
  } catch (error) {
    console.error('Failed to send email OTP:', error);
    return null;
  }
};

// export const sendPhoneOtp = async ({destination, otp }: SendOtpOptions) => {
//   try {
//     const response = await apiClient.post('/api/send-sms-otp', {
//       phone: destination,
//       otp,
//     });

//     return response.data.success;
//   } catch (error) {
//     console.error('Failed to send phone OTP:', error);
//     return false;
//   }
// };
