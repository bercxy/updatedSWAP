<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SecurityTest extends TestCase {

  public function testBatchMaxConstantIsReasonable(): void {
    $this->assertGreaterThan(0, BATCH_MAX);
    $this->assertLessThanOrEqual(100, BATCH_MAX);
  }
}
