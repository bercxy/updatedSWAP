<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_post();

  $email = trim((string)($_POST['email'] ?? ''));
  $pass  = (string)($_POST['password'] ?? '');

  $pdo = db();
  $stmt = $pdo->prepare("SELECT id, email, password_hash, role FROM users WHERE email=?");
  $stmt->execute([$email]);
  $u = $stmt->fetch();

  if ($u && password_verify($pass, $u['password_hash'])) {
    $_SESSION['user_id'] = (int)$u['id'];
    $_SESSION['role'] = (string)$u['role'];
  if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor') {
    header('Location: admin_queue.php');
  } else {
    header('Location: user_booking_demo.php');
  }
exit;
  }
  $err = 'Invalid login';
}

if (!empty($_SESSION['user_id'])) {
  $role = $_SESSION['role'] ?? '';

  if ($role === 'admin' || $role === 'supervisor') {
    header('Location: admin_queue.php');
  } else {
    header('Location: user_booking_demo.php');
  }
  exit;
}



function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 24px; }
    .box { max-width: 420px; border:1px solid #ddd; padding: 16px; border-radius: 10px; }
    label { display:block; margin-top: 10px; }
    input { width:100%; padding:10px; box-sizing: border-box; }
    button { margin-top: 14px; padding:10px 14px; }
    .error { color:#b00020; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Login (Admin Demo)</h2>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"/>
      <label>Email</label>
      <input name="email" type="email" required/>
      <label>Password</label>
      <input name="password" type="password" required/>
      <button type="submit">Login</button>
      <?php if ($err): ?><div class="error"><?= h($err) ?></div><?php endif; ?>
    </form>
  </div>
</body>
</html>
