<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function audit_log_batch(int $adminId, string $action, array $bookingIds, string $ip, string $ua): void {
  $pdo = db();

  $ip = mb_substr($ip, 0, 60);
  $ua = mb_substr($ua, 0, 255);

  $stmt = $pdo->prepare(
    "INSERT INTO audit_logs (actor_user_id, action, target_type, target_ids_json, ip_addr, user_agent, created_at)
     VALUES (?, ?, 'booking_batch', ?, ?, ?, NOW())"
  );

  $stmt->execute([
    $adminId,
    $action,
    json_encode(array_values($bookingIds), JSON_UNESCAPED_SLASHES),
    $ip,
    $ua
  ]);
}

