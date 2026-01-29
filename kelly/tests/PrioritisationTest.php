<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PrioritisationTest extends TestCase {

  public function testStaffHigherThanStudent(): void {
    $t = date('Y-m-d H:i:s', time() - 3600);
    $staff = Prioritisation::score('staff', 'normal', $t);
    $student = Prioritisation::score('student', 'normal', $t);
    $this->assertGreaterThan($student, $staff);
  }

  public function testUrgentAddsScore(): void {
    $t = date('Y-m-d H:i:s', time() - 3600);
    $urgent = Prioritisation::score('student', 'urgent', $t);
    $normal = Prioritisation::score('student', 'normal', $t);
    $this->assertGreaterThan($normal, $urgent);
  }

  public function testOlderGetsHigherScore(): void {
    $older = date('Y-m-d H:i:s', time() - 3600*6);
    $newer = date('Y-m-d H:i:s', time() - 3600*1);
    $s1 = Prioritisation::score('student', 'normal', $older);
    $s2 = Prioritisation::score('student', 'normal', $newer);
    $this->assertGreaterThan($s2, $s1);
  }
}
