<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/prioritisation.php';

requireSupervisorOrAdmin();

$pdo = db();

$pending = $pdo->query("SELECT id, requester_type, purpose_tag, original_submitted_at
                        FROM bookings WHERE status='pending'")
               ->fetchAll();

$update = $pdo->prepare("UPDATE bookings SET priority_score=? WHERE id=?");
foreach ($pending as $b) {
  $score = Prioritisation::score($b['requester_type'], $b['purpose_tag'], $b['original_submitted_at']);
  $update->execute([$score, (int)$b['id']]);
}

$sort = $_GET['sort'] ?? 'name';

switch ($sort) {
  case 'priority':
    $orderBy = 'priority_score DESC, original_submitted_at ASC';
    break;

  case 'oldest':
    $orderBy = 'original_submitted_at ASC';
    break;

  case 'name':
  default:
    $orderBy = "
      CASE requester_type
        WHEN 'admin' THEN 1
        WHEN 'supervisor' THEN 2
        ELSE 3
      END,
      requester_email ASC
    ";
    break;
}


$stmt = $pdo->query("SELECT b.id, b.requester_type, b.purpose_tag, b.original_submitted_at,
                            b.priority_score, u.email AS requester_email
                     FROM bookings b
                     JOIN users u ON u.id = b.requester_user_id
                     WHERE b.status='pending'
                     ORDER BY $orderBy");

$rows = $stmt->fetchAll();

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Queue - Smart Prioritisation & Batch Processing</title>
  <style>
    
    .row { display:flex; gap:12px; align-items: center; }
    .error { color:#b00020; margin-top: 8px; }
    .ok { color:#0b6; margin-top: 8px; }
    body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 32px;
  }

  .box {
    background: #fff;
    max-width: 900px;
    margin: auto;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  }

  h2 {
    margin-top: 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
  }

  th {
    background: #111;
    color: #fff;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.05em;
  }

  th, td {
    padding: 12px;
    border-bottom: 1px solid #eee;
  }

  tr:hover {
    background: #fafafa;
  }

  .actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
  }

  .btn {
    padding: 12px 18px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
  }

  .btn.primary {
    background: #1a73e8;
    color: white;
  }

  .btn.danger {
    background: #d93025;
    color: white;
  }

  .pill {
    background: #e8f0fe;
    color: #1a73e8;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
  }

  .priority {
  padding: 6px 12px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 13px;
  display: inline-block;
  min-width: 48px;
  text-align: center;
}

.priority-high {
  background-color: #d93025; 
  color: #fff;
}

.priority-mid {
  background-color: #fbbc04; 
  color: #111;
}

.priority-low {
  background-color: #34a853; 
  color: #fff;
}

.form-wrapper {
  max-width: 900px;
  margin: 40px auto;
  background: #ffffff;
  padding: 24px 28px;
  border-radius: 14px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.actions {
  display: flex;
  justify-content: center; 
  gap: 16px;
  margin-top: 28px;
}

.btn.approve {
  background: #1e8e3e; 
  color: #fff;
}

.btn.approve:hover {
  background: #188038;
}

.btn.reject {
  background: #d93025; 
  color: #fff;
}

.btn.reject:hover {
  background: #b3261e;
}

.notice {
  margin-top: 20px;
  padding: 14px 18px;
  border-radius: 10px;
  font-weight: 500;
  text-align: center;
}

.notice.success {
  background: #e6f4ea;
  color: #137333;
}

.notice.error {
  background: #fce8e6;
  color: #b3261e;
}

select {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 13px;
}
  </style>
</head>
<body>

<div class="topbar">
  <div class="row">
    <strong>Admin Queue</strong>
  <div style="margin-left:auto" class="row">
    <form method="get" class="row">
      <label for="sort" style="font-size:13px;">Sort by:</label>
      <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="name" <?= ($_GET['sort'] ?? '') === 'name' ? 'selected' : '' ?>>
          Name
        </option>
        <option value="priority" <?= ($_GET['sort'] ?? '') === 'priority' ? 'selected' : '' ?>>
          Priority
        </option>
        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>
          Age
        </option>
      </select>
    </form>
<a class="btn" href="logout.php">Logout</a>

  </div>
</div>

<div class="form-wrapper">
<form method="post" action="admin_batch_action.php">
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"/>

  <table>
    <thead>
      <tr>
        <th style="width:48px;">Pick</th>
        <th>ID</th>
        <th>Requester</th>
        <th>Type</th>
        <th>Purpose</th>
        <th>Original Submitted</th>
        <th>Priority Score</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="7">No pending bookings.</td></tr>
    <?php else: ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <input type="checkbox" name="booking_ids[]" value="<?= (int)$r['id'] ?>"/>
          </td>
          <td><?= (int)$r['id'] ?></td>
          <td><?= h((string)$r['requester_email']) ?></td>
          <td><span class="pill"><?= h((string)$r['requester_type']) ?></span></td>
          <td><span class="pill"><?= h((string)$r['purpose_tag']) ?></span></td>
          <td><?= h((string)$r['original_submitted_at']) ?></td>
          <?php
          $score = (int)$r['priority_score'];
          if ($score >= 80) {
            $priorityClass = 'priority-high';
          } elseif ($score >= 40) {
            $priorityClass = 'priority-mid';
          } else {
            $priorityClass = 'priority-low';
          }
          ?>
          <td>
            <span class="priority <?= $priorityClass ?>">
              <?= $score ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="actions">
    <button class="btn approve" type="submit" name="action" value="approve">Batch Approve</button>
    <button class="btn reject" type="submit" name="action" value="reject">Batch Reject</button>
  </div>

  <div class="muted" style="margin-top:10px;">
    Batch limit: <?= (int)BATCH_MAX ?> items per action.
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="notice success"><?= h((string)$_GET['msg']) ?></div>
  <?php endif; ?>
  <?php if (!empty($_GET['err'])): ?>
    <div class="notice error"><?= h((string)$_GET['err']) ?></div>
  <?php endif; ?>

</form>
</div>

</body>
</html>
