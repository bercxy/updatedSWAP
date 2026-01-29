<?php
declare(strict_types=1);

function csrf_token(): string {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_verify_post(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();

  $token = (string)($_POST['csrf'] ?? '');
  if (empty($_SESSION['csrf']) || !hash_equals((string)$_SESSION['csrf'], $token)) {
    http_response_code(403);
    exit('CSRF failed');
  }
}
