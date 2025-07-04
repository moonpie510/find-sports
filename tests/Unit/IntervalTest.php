<?php

namespace Tests\Unit;

use App\Classes\Interval;
use Exception;
use PHPUnit\Framework\TestCase;

class IntervalTest extends TestCase
{

    public function test_interval_create_success(): void
    {
        Interval::create('2030-06-25T12:00:00', '2030-06-26T12:00:00');

        $this->assertTrue(true);
    }

    public function test_interval_create_when_start_gt_end(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Время начала не может быть больше времени конца');
        Interval::create('2030-06-25T12:00:00', '2030-06-22T12:00:00');
    }

    public function test_interval_create_when_start_eq_end(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Время начала не может быть равно времени конца');
        Interval::create('2030-06-25T12:00:00', '2030-06-25T12:00:00');
    }

    public function test_interval_create_when_start_less_now(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Время начала не может быть меньше текущего времени');
        Interval::create('2007-06-25T12:00:00', '2007-06-28T12:00:00');
    }
}
