<?php

namespace AvtoDev\B2BApiLaravel\Tests\Traits;

use AvtoDev\B2BApiLaravel\Tests\AbstractUnitTestCase;
use AvtoDev\B2BApiLaravel\Tests\Traits\Mocks\InstanceableTraitMock;

/**
 * Class InstanceableTraitTest.
 */
class InstanceableTraitTest extends AbstractUnitTestCase
{
    /**
     * Тест методов трейта.
     *
     * @return void
     */
    public function testMethods()
    {
        $mock = new InstanceableTraitMock;

        $this->assertInstanceOf(InstanceableTraitMock::class, $mock->instance());
    }
}
