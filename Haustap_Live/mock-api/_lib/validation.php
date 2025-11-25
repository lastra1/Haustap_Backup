<?php
function ht_validate_email(string $email): bool {
  return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}
function ht_validate_mobile(string $mobile): bool {
  return (bool) preg_match('/^\+?[0-9]{10,15}$/', $mobile);
}
function ht_validate_password(string $password): bool {
  return strlen($password) >= 6;
}
function ht_validate_birthdate($y, $m, $d): bool {
  $yy = (int) $y; $mm = (int) $m; $dd = (int) $d;
  if ($yy < 1900 || $yy > (int)date('Y')) return false;
  return checkdate($mm, $dd, $yy);
}
function ht_collect_register_errors(array $data): array {
  $errors = [];
  $firstName = trim($data['firstName'] ?? '');
  $lastName  = trim($data['lastName'] ?? '');
  $email     = trim($data['email'] ?? '');
  $mobile    = trim($data['mobile'] ?? '');
  $password  = (string) ($data['password'] ?? '');
  $confirmPassword = (string) ($data['confirmPassword'] ?? '');
  $birthMonth = (string) ($data['birthMonth'] ?? '');
  $birthDay   = (string) ($data['birthDay'] ?? '');
  $birthYear  = (string) ($data['birthYear'] ?? '');

  if ($firstName === '') $errors['firstName'] = 'First name is required';
  if ($lastName === '')  $errors['lastName']  = 'Last name is required';
  if ($email === '')     $errors['email']     = 'Email is required';
  elseif (!ht_validate_email($email)) $errors['email'] = 'Invalid email format';
  if ($mobile === '')    $errors['mobile']    = 'Mobile is required';
  elseif (!ht_validate_mobile($mobile)) $errors['mobile'] = 'Mobile must be 10–15 digits';
  if ($password === '')  $errors['password']  = 'Password is required';
  elseif (!ht_validate_password($password)) $errors['password'] = 'Password must be at least 6 characters';
  if ($confirmPassword === '') $errors['confirmPassword'] = 'Confirm password is required';
  elseif ($password !== $confirmPassword) $errors['confirmPassword'] = 'Passwords do not match';
  if (!ht_validate_birthdate($birthYear, $birthMonth, $birthDay)) {
    $errors['birthdate'] = 'Birth date is invalid';
  }

  return $errors;
}

