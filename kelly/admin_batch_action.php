<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/audit.php';
require_once __DIR__ . '/batch.php';

requireAdmin();
csrf_verify_post();

$action = (string)($_POST['action'] ?? '');
$rawIds = $_POST['booking_ids'] ?? [];

try {
  $action = Batch::validateAction($action);

  if (!is_array($rawIds)) throw new InvalidArgumentException('Invalid IDs');
  $ids = Batch::normaliseIds($rawIds);
  Batch::validateBatchSize($ids);

  $pdo = db();

  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT id FROM bookings WHERE id IN ($placeholders) AND status='pending'");
  $stmt->execute($ids);
  $eligible = $stmt->fetchAll(PDO::FETCH_COLUMN);

  if (count($eligible) !== count($ids)) {
    throw new InvalidArgumentException('One or more bookings are not eligible (must be pending).');
  }

  $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

  $pdo->beginTransaction();
  try {
    $sql = "UPDATE bookings
            SET status=?, decided_by=?, decided_at=NOW()
            WHERE id IN ($placeholders) AND status='pending'";
    $stmt2 = $pdo->prepare($sql);
    $params = array_merge([$newStatus, currentUserId()], $ids);
    $stmt2->execute($params);

    audit_log_batch(
      currentUserId(),
      $action,
      $ids,
      (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
      (string)($_SERVER['HTTP_USER_AGENT'] ?? 'unknown')
    );

    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }

  $actionText = ($action === 'approve') ? 'approved' : 'rejected';
  $message = count($ids) . " booking(s) successfully $actionText.";
  header('Location: admin_queue.php?msg=' . urlencode($message));
  exit;
} catch (Throwable $e) {
  header('Location: admin_queue.php?err=' . urlencode($e->getMessage()));
  exit;
}
