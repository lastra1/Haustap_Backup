export const handleSignUp = (form) => {
  if (!form.email || !form.password) {
    alert("Please fill all required fields.");
    return;
  }

  if (form.password !== form.confirmPassword) {
    alert("Passwords do not match!");
    return;
  }

  alert(`Welcome, ${form.firstName || "User"}! Your account is ready.`);
};
