<?php
declare(strict_types=1);

final class Prioritisation {
  public static function score(string $requesterType, string $purposeTag, string $originalSubmittedAt): int {
  $score = 0;

  $requesterType = strtolower(trim($requesterType));
  $purposeTag = strtolower(trim($purposeTag));

  if ($requesterType === 'staff')   $score += 40;
  if ($requesterType === 'student') $score += 20;

  if ($purposeTag === 'urgent') $score += 20;

  $t = strtotime($originalSubmittedAt);
  if ($t !== false) {
    $ageMinutes = (int) floor((time() - $t) / 60);
    $score += max(0, min(20, (int) floor($ageMinutes / 30)));
  }

  return min(100, $score);
  }
}