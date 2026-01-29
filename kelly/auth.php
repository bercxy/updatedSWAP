<?php
declare(strict_types=1);

function session_start_safe(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
}

function requireLogin(): void {
  session_start_safe();
  if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthenticated');
  }
}

function requireAdmin(): void {
  requireLogin();
  if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Forbidden');
  }
}

function currentUserId(): int {
  session_start_safe();
  return (int)($_SESSION['user_id'] ?? 0);
}

function currentRole(): string {
  session_start_safe();
  return (string)($_SESSION['role'] ?? '');
}

function requireRole(array $allowed): void {
  requireLogin();
  $role = $_SESSION['role'] ?? '';
  if (!in_array($role, $allowed, true)) {
    http_response_code(403);
    exit('Forbidden');
  }
}

function requireSupervisorOrAdmin(): void {
  requireRole(['supervisor', 'admin']);
}
