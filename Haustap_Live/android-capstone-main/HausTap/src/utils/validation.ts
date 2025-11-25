export const isValidEmail = (email: string): boolean => {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
};

export const isStrongPassword = (password: string): boolean => {
    // At least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return passwordRegex.test(password);
};

export const isValidPhoneNumber = (phone: string): boolean => {
    const phoneRegex = /^(09\d{9}|\+639\d{9})$/;
    return phoneRegex.test(phone);
};

export const isNonEmptyString = (value: string | null | undefined): boolean => {
    return value !== null && value !== undefined && value.trim().length > 0;
}
