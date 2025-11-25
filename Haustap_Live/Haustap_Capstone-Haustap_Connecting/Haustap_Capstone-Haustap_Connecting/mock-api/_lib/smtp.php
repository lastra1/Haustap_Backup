<?php
function smtp_send($host, $port, $secure, $user, $pass, $fromEmail, $fromName, $toEmail, $subject, $body)
{
  $target = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . (int)$port;
  $fp = @stream_socket_client($target, $errno, $errstr, 15);
  if (!$fp) { return false; }
  $read = function() use ($fp) {
    $line = '';
    $resp = '';
    while (($line = fgets($fp, 515)) !== false) {
      $resp .= $line;
      if (strlen($line) < 4 || $line[3] !== '-') { break; }
    }
    return $resp;
  };
  $send = function($cmd) use ($fp) { fwrite($fp, $cmd . "\r\n"); };
  $read();
  $send('EHLO localhost');
  $read();
  if ($secure === 'tls') {
    $send('STARTTLS');
    $read();
    if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($fp); return false; }
    $send('EHLO localhost');
    $read();
  }
  $send('AUTH LOGIN');
  $read();
  $send(base64_encode($user));
  $read();
  $send(base64_encode($pass));
  $authResp = $read();
  if (strpos($authResp, '235') === false) { fclose($fp); return false; }
  $send('MAIL FROM:<' . $fromEmail . '>');
  $mailFromResp = $read();
  if (strpos($mailFromResp, '250') === false) { fclose($fp); return false; }
  $send('RCPT TO:<' . $toEmail . '>');
  $rcptResp = $read();
  if (strpos($rcptResp, '250') === false) { fclose($fp); return false; }
  $send('DATA');
  $read();
  $headers = [];
  $headers[] = 'From: ' . ($fromName ? ($fromName . ' <' . $fromEmail . '>') : ('<' . $fromEmail . '>'));
  $headers[] = 'To: <' . $toEmail . '>';
  $headers[] = 'Subject: ' . $subject;
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'Content-Type: text/plain; charset=UTF-8';
  $payload = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.";
  $send($payload);
  $dataResp = $read();
  $send('QUIT');
  fclose($fp);
  return strpos($dataResp, '250') !== false;
}