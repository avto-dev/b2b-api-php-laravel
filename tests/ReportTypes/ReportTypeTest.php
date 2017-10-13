<?php

namespace AvtoDev\B2BApiLaravel\Tests\ReportTypes;

use AvtoDev\B2BApiLaravel\ReportTypes\ReportType;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\Tests\AbstractUnitTestCase;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class ReportTypeTest.
 */
class ReportTypeTest extends AbstractUnitTestCase
{
    /**
     * @var ReportType
     */
    protected $instance;

    /**
     * Тест наследований класса.
     *
     * @return void
     */
    public function testImplements()
    {
        $this->assertInstanceOf(ReportTypeInterface::class, $this->instance);
        $this->assertInstanceOf(Arrayable::class, $this->instance);
        $this->assertInstanceOf(Jsonable::class, $this->instance);
    }

    /**
     * Тест метода `configure()`.
     */
    public function testConfigure()
    {
        $instance = new ReportType('some value');
        $this->assertEquals('some value', $instance->getUid());

        $instance = new ReportType(['some value']);
        $this->assertNull($instance->getUid());

        $instance = new ReportType(new ReportType('some'));
        $this->assertEquals('some', $instance->getUid());

        $instance = new ReportType(['value' => 'some', 'desc' => 'Some Description', 'name' => 'aaa']);
        $this->assertEquals('some', $instance->getUid());
        $this->assertEquals('Some Description', $instance->getDescription());
        $this->assertEquals('aaa', $instance->getName());

        $instance = new ReportType(new ReportType(['value' => 'some', 'desc' => 'Some Description', 'name' => 'aaa']));
        $this->assertEquals('some', $instance->getUid());
        $this->assertEquals('Some Description', $instance->getDescription());
        $this->assertEquals('aaa', $instance->getName());

        $instance = new ReportType(['id' => 'some2', 'description' => 'Some Description2']);
        $this->assertEquals('some2', $instance->getUid());
        $this->assertEquals('Some Description2', $instance->getDescription());

        $instance = new ReportType(['uid' => 'some3']);
        $this->assertEquals('some3', $instance->getUid());
    }

    /**
     * Тест конвертации в строку.
     *
     * @return void
     */
    public function testToStringMethod()
    {
        $instance = new ReportType(['uid' => 'some']);
        $this->assertEquals('some', (string) $instance);
    }

    /**
     * Тест методов-акцессоров.
     *
     * @return void
     */
    public function testGettersAndSetters()
    {
        $this->assertInstanceOf(ReportType::class, $this->instance->setUid('  aaa '));
        $this->assertEquals('aaa', $this->instance->getUid());

        $this->assertInstanceOf(ReportType::class, $this->instance->setName('       bbb   '));
        $this->assertEquals('bbb', $this->instance->getName());

        $this->assertInstanceOf(ReportType::class, $this->instance->setDescription('          ccc  '));
        $this->assertEquals('ccc', $this->instance->getDescription());

        // Тест преобразования в строку
        $this->instance->setName(111);
        $this->assertEquals('111', $this->instance->getName());
        // Тест возможности установить null
        $this->instance->setName(null);
        $this->assertNull($this->instance->getName());

        // Тест преобразования в строку
        $this->instance->setUid(111);
        $this->assertEquals('111', $this->instance->getUid());
        // Тест возможности установить null
        $this->instance->setUid(null);
        $this->assertNull($this->instance->getUid());

        // Тест преобразования в строку
        $this->instance->setDescription(111);
        $this->assertEquals('111', $this->instance->getDescription());
        // Тест возможности установить null
        $this->instance->setDescription(null);
        $this->assertNull($this->instance->getDescription());
    }

    /**
     * Тест преобразования объекта в json-строку и массив.
     */
    public function testToArrayAndToJsonConvertation()
    {
        $this->instance->configure([
            'uid'  => 'aaa',
            'name' => 'bbb',
            'desc' => 'ccc',
        ]);

        $this->assertEquals($array = [
            'uid'         => 'aaa',
            'name'        => 'bbb',
            'description' => 'ccc',
        ], $this->instance->toArray());

        $this->assertJson($json = $this->instance->toJson());
        $this->assertEquals($array, json_decode($json, true));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ReportType;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->instance);

        parent::tearDown();
    }
}
