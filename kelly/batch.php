<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

final class Batch {
  public static function normaliseIds(array $raw): array {
    $ids = array_map(static fn($v) => (int)$v, $raw);
    $ids = array_filter($ids, static fn($v) => $v > 0);
    $ids = array_values(array_unique($ids));
    return $ids;
  }

  public static function validateBatchSize(array $ids): void {
    $n = count($ids);
    if ($n < 1) throw new InvalidArgumentException('No IDs selected');
    if ($n > BATCH_MAX) throw new InvalidArgumentException('Batch too large');
  }

  public static function validateAction(string $action): string {
    $action = strtolower(trim($action));
    if (!in_array($action, ['approve', 'reject'], true)) {
      throw new InvalidArgumentException('Invalid action');
    }
    return $action;
  }
}
