<?php

use PHPUnit\Framework\TestCase;

use Framewub\Services;

class MockService
{
}

class RegMockService
{
}

class ServicesTest extends TestCase
{
    public function testGet()
    {
        $service = Services::get(MockService::class);
        $this->assertInstanceOf(MockService::class, $service);
    }

    public function testSingleton()
    {
        $service = Services::get(MockService::class);
        $service2 = Services::get(MockService::class);
        $this->assertTrue($service === $service2);
    }

    public function testRegister()
    {
        Services::register('Mock', function () { return new RegMockService(); });
        $service = Services::get('Mock');
        $this->assertInstanceOf(RegMockService::class, $service);
    }
}
