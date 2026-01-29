<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/prioritisation.php';

$pdo = db();

// Create demo admin + user (safe: only if not exist)
function ensureUser(PDO $pdo, string $email, string $pass, string $role): int {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
  $stmt->execute([$email]);
  $row = $stmt->fetch();
  if ($row) return (int)$row['id'];

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $stmt2 = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
  $stmt2->execute([$email, $hash, $role]);
  return (int)$pdo->lastInsertId();
}

$adminId = ensureUser($pdo, 'admin@example.com', 'AdminPass123!', 'admin');
$userId  = ensureUser($pdo, 'user@example.com',  'UserPass123!',  'user');
$supervisorId = ensureUser($pdo, 'supervisor@example.com', 'SupervisorPass123!', 'supervisor');


$ins = $pdo->prepare("INSERT INTO bookings
  (requester_user_id, requester_type, purpose_tag, original_submitted_at, status, priority_score)
  VALUES (?, ?, ?, ?, 'pending', ?)");

$examples = [
  [$userId, 'student', 'normal', date('Y-m-d H:i:s', time() - 60*60*5)],   // 5h ago
  [$userId, 'student', 'urgent', date('Y-m-d H:i:s', time() - 60*60*2)],   // 2h ago urgent
  [$adminId, 'staff',  'normal', date('Y-m-d H:i:s', time() - 60*60*1)],   // 1h ago staff
  [$adminId, 'staff',  'urgent', date('Y-m-d H:i:s', time() - 60*60*10)],  // 10h ago staff urgent
];

foreach ($examples as [$uid,$type,$tag,$t]) {
  $score = Prioritisation::score($type, $tag, $t);
  $ins->execute([$uid, $type, $tag, $t, $score]);
}

echo "Seeded.\nAdmin: admin@example.com / AdminPass123!\n";
