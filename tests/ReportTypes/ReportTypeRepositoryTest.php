<?php

namespace AvtoDev\B2BApiLaravel\Tests\ReportTypes;

use AvtoDev\B2BApiLaravel\ReportTypes\ReportType;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;
use AvtoDev\B2BApiLaravel\Tests\AbstractUnitTestCase;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

/**
 * Class ReportTypeRepositoryTest.
 */
class ReportTypeRepositoryTest extends AbstractUnitTestCase
{
    /**
     * @var ReportTypesRepository
     */
    protected $instance;

    /**
     * Тест наследований класса.
     *
     * @return void
     */
    public function testImplements()
    {
        $this->assertInstanceOf(Collection::class, $this->instance);
        $this->assertInstanceOf(Arrayable::class, $this->instance);
        $this->assertInstanceOf(Jsonable::class, $this->instance);
    }

    /**
     * Тест конструктора.
     */
    public function testConstructor()
    {
        // Если передать просто одну строку
        $instance = new ReportTypesRepository('uid_0');
        $this->assertEquals('uid_0', $instance->first());
        $this->assertCount(1, $instance);

        // ... массив строк
        $instance = new ReportTypesRepository(['uid_0', 'uid_1', 123]);
        $this->assertEquals('uid_0', $instance->getByUid('uid_0')->getUid());
        $this->assertEquals('uid_1', $instance->getByUid('uid_1')->getUid());
        $this->assertEquals(123, $instance->getByUid('123')->getUid());
        $this->assertNull($instance->getByUid('none'));
        $this->assertCount(3, $instance);

        // ... массив массивов
        $instance = new ReportTypesRepository([
            'uid_0',
            'uid_1' => ['uid_1.1'],
            ['uid' => 'uid_2', 'name' => 'some name'],
            'uid_3' => ['id' => 'uid_3.1', 'desc' => 'Description']
        ]);
        $this->assertTrue($instance->hasUid('uid_0'));
        $this->assertFalse($instance->hasUid('uid_1'));
        $this->assertFalse($instance->hasUid('uid_1.1'));
        $this->assertTrue($instance->hasUid('uid_2'));
        $this->assertEquals('some name', $instance->getByUid('uid_2')->getName());
        $this->assertTrue($instance->hasUid('uid_3.1'));
        $this->assertEquals('Description', $instance->getByUid('uid_3.1')->getDescription());
    }

    /**
     * Тест дополнительных методов.
     *
     * @return void
     */
    public function testAdditionalMethods()
    {
        $this->instance->push(new ReportType(['uid' => 'uid1', 'name' => 'name1']));
        $this->instance->push(new ReportType(['uid' => 'uid2', 'name' => 'name2']));

        $this->assertTrue($this->instance->hasUid('uid1'));
        $this->assertTrue($this->instance->hasUid('uid2'));
        $this->assertFalse($this->instance->hasUid('none'));
        $this->assertInstanceOf(ReportType::class, $this->instance->getByUid('uid1'));

        $this->assertTrue($this->instance->hasName('name1'));
        $this->assertTrue($this->instance->hasName('name2'));
        $this->assertFalse($this->instance->hasName('none'));
        $this->assertInstanceOf(ReportType::class, $this->instance->getByName('name1'));

        foreach (['uid1', 'uid2'] as $uid) {
            $this->assertContains($uid, $this->instance->getAllUids());
        }

        foreach (['name1', 'name2'] as $name) {
            $this->assertContains($name, $this->instance->getAllNames());
        }
    }

    /**
     * Тест преобразования объекта в json-строку и массив.
     */
    public function testToArrayAndToJsonConvertation()
    {
        $instance = new ReportTypesRepository([
            'name1' => ['uid' => 'uid1'],
            ['uid' => 'uid2', 'name' => 'name2'],
        ]);

        $array = $instance->toArray();

        $this->assertEquals('uid1', $array[0]['uid']);
        $this->assertEquals('name1', $array[0]['name']);
        $this->assertEquals('uid2', $array[1]['uid']);
        $this->assertEquals('name2', $array[1]['name']);

        $this->assertJson($instance->toJson());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ReportTypesRepository;
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
