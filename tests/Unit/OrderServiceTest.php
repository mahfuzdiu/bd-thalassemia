<?php

namespace Tests\Unit;

use App\Services\OrderService;
use PHPUnit\Framework\TestCase;
use App\Enums\OrderStatusEnum;
use ReflectionClass;

class OrderServiceTest extends TestCase
{

    /**
     * A basic unit test example.
     */
    public function test_getNextStatusToUpdate(): void
    {
        $os = new OrderService();
        $actual = $os->getNextStatusToUpdate('processing');
        $this->assertEquals('shipped', $actual);
    }

    public function test_createOrderNum()
    {
        $service = new OrderService();

        $orderNum = $service->createOrderNum();

        // Check it is a string
        $this->assertIsString($orderNum);

        // Check the date prefix matches Ymd
        $datePart = substr($orderNum, 0, 8);
        $this->assertEquals(now()->format('Ymd'), $datePart);

        // Check last 6 characters are uppercase alphanumeric
        $uniquePart = substr($orderNum, -6);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $uniquePart);
    }
}
