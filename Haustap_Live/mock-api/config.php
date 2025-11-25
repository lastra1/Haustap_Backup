<?php
return [
  'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
  'smtp_port' => (int)(getenv('SMTP_PORT') ?: 465),
  'smtp_secure' => getenv('SMTP_SECURE') ?: 'ssl',
  'smtp_user' => getenv('SMTP_USER') ?: '',
  'smtp_pass' => getenv('SMTP_PASS') ?: '',
  'from_email' => getenv('SMTP_FROM') ?: (getenv('SMTP_USER') ?: ''),
  'from_name' => getenv('SMTP_FROM_NAME') ?: 'HausTap'
];