import apiClient from "./api-client";


/**
 * Login API call
 * @param {string} email
 * @param {string} password
 */
export const login = async (email, password) => {
    try {
  
        const response = await apiClient.post("/api/auth/login", {
            email,
            password,
        }); 
        return response.data;


    } catch (error) {
        throw error.response?.data || { message: "Network Error" };
    }
}

/** Register API call
 * @param {string} name
 * @param {string} email
 * @param {string} phone
 * @param {string} password
 */
export const register = async (name, email, phone, password, confirmPassword) => {
    try {
        const response = await apiClient.post("/api/auth/register", {
            name,
            email,
            phone,
            password,
            confirmPassword
        });
        return response.data;
    } catch (error) {
        throw error.response?.data || { message: "Network Error" };
    }
}

/**
 * Send OTP to email for password reset
 */
export const sendOtp = async (email) => {
    try {
        const response = await apiClient.post('/api/auth/otp/send', { email });
        return response.data;
    } catch (error) {
        throw error.response?.data || { message: 'Network Error' };
    }
};

/**
 * Reset password using OTP
 */
export const resetPassword = async (email, otp, password, confirmPassword) => {
    try {
        const response = await apiClient.post('/api/auth/password/reset', {
            email,
            otp,
            createnew_password: password,
            password_confirmation: confirmPassword, // FIXED
        });
        return response.data;
    } catch (error) {
        throw error.response?.data || { message: 'Network Error' };
    }
};
