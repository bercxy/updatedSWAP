<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BatchTest extends TestCase {

  public function testNormaliseIds(): void {
    $raw = ['1','2','2','-5','abc', 3];
    $ids = Batch::normaliseIds($raw);
    $this->assertSame([1,2,3], $ids);
  }

  public function testValidateAction(): void {
    $this->assertSame('approve', Batch::validateAction('approve'));
    $this->assertSame('reject', Batch::validateAction('ReJeCt'));
  }

  public function testValidateActionRejects(): void {
    $this->expectException(InvalidArgumentException::class);
    Batch::validateAction('delete');
  }

  public function testRejectsEmptyBatch(): void {
    $this->expectException(InvalidArgumentException::class);
    Batch::validateBatchSize([]);
  }

  public function testRejectsOversizeBatch(): void {
    $ids = range(1, BATCH_MAX + 1);
    $this->expectException(InvalidArgumentException::class);
    Batch::validateBatchSize($ids);
  }
}
