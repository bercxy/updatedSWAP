<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/prioritisation.php';

requireLogin();
$pdo = db();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_verify_post();

  $requesterType = (string)($_POST['requester_type'] ?? 'student');
  $purposeTag    = (string)($_POST['purpose_tag'] ?? 'normal');

  if (!in_array($requesterType, ['student','staff'], true)) $requesterType = 'student';
  if (!in_array($purposeTag, ['normal','urgent'], true)) $purposeTag = 'normal';

  $original = date('Y-m-d H:i:s');
  $score = Prioritisation::score($requesterType, $purposeTag, $original);

  try {
    $stmt = $pdo->prepare(
      "INSERT INTO bookings
       (requester_user_id, requester_type, purpose_tag, original_submitted_at, status, priority_score)
       VALUES (?, ?, ?, ?, 'pending', ?)"
    );
    $stmt->execute([currentUserId(), $requesterType, $purposeTag, $original, $score]);
    $msg = "Booking submitted (pending).";
  } catch (Throwable $e) {
    $err = "Failed to submit booking.";
  }
}

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>User Booking (Demo)</title>
  <style>
    body{font-family:Arial,sans-serif;margin:24px}
    .box{max-width:520px;border:1px solid #ddd;padding:16px;border-radius:10px}
    label{display:block;margin-top:10px}
    select,button{padding:10px;width:100%;box-sizing:border-box}
    .ok{color:#0b6;margin-top:10px}
    .error{color:#b00020;margin-top:10px}
    a{display:inline-block;margin-top:10px}
    body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 32px;
  }
  </style>
</head>
<body>
  <div class="box">
    <h2>User Booking Page (Demo)</h2>

    <form method="post">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"/>

      <label>Requester Type</label>
      <select name="requester_type">
        <option value="student">student</option>
        <option value="staff">staff</option>
      </select>

      <label>Purpose Tag</label>
      <select name="purpose_tag">
        <option value="normal">normal</option>
        <option value="urgent">urgent</option>
      </select>

      <button type="submit">Submit Booking</button>
    </form>

    <?php if ($msg): ?><div class="ok"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="error"><?= h($err) ?></div><?php endif; ?>

    <a href="logout.php">Logout</a>
  </div>
</body>
</html>
