<?php

namespace AvtoDev\B2BApiLaravel\Tests;

use AvtoDev\B2BApi\Clients\v1\Client;
use AvtoDev\B2BApiLaravel\B2BApiService;
use AvtoDev\B2BApi\References\QueryTypes;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportType;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportData;
use AvtoDev\B2BApi\Exceptions\B2BApiInvalidArgumentException;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportStatusData;
use AvtoDev\B2BApiLaravel\Exceptions\InvalidReportTypeException;

class B2BApiServiceTest extends AbstractUnitTestCase
{
    /**
     * @var B2BApiService
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->instance = resolve(B2BApiService::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->instance);

        parent::tearDown();
    }

    /**
     * Тест метода `client()`.
     */
    public function testClient()
    {
        $this->assertInstanceOf(Client::class, $this->instance->client());
    }

    /**
     * Тест метода `queryTypes()`.
     */
    public function testQueryTypes()
    {
        $this->assertInstanceOf(QueryTypes::class, $this->instance->queryTypes());
    }

    /**
     * Тест метода `makeReport()`.
     */
    public function testMakeReport()
    {
        $this->assertInstanceOf(ReportStatusData::class, $this->instance->makeReport(
            'GRZ',
            'A123AA177',
            'default'
        ));

        // Тест того, что метод не валится, если ему не передать ReportType
        $this->assertInstanceOf(ReportStatusData::class, $this->instance->makeReport(
            'GRZ',
            'A123AA177'
        ));
    }

    /**
     * Тест метода `makeReport()` с не корректным типом.
     */
    public function testMakeReportWithInvalidQueryType()
    {
        $this->expectException(B2BApiInvalidArgumentException::class);

        $this->assertInstanceOf(ReportStatusData::class, $this->instance->makeReport(
            'bla bla',
            'A123AA177',
            'default'
        ));
    }

    /**
     * Тест метода `generateAuthToken()`.
     */
    public function testGenerateAuthToken()
    {
        $this->assertNotEmpty($this->instance->generateAuthToken());
    }

    /**
     * Тест метода `getReportTypeUid()`.
     */
    public function testGetReportTypeUid()
    {
        $this->assertEquals(
            '%some_report_type_uid_here%',
            $this->instance->getReportTypeUid('default')
        );
        $this->assertEquals(
            '%some_report_type_uid_here%',
            $this->instance->getReportTypeUid('%some_report_type_uid_here%')
        );

        $report_type = new ReportType;
        $report_type->setUid('aaa');
        $this->assertEquals(
            'aaa',
            $this->instance->getReportTypeUid($report_type)
        );
    }

    /**
     * Тест метода `getReportTypeUid()` в некорректным значением.
     */
    public function testGetReportTypeUidWithInvalidArgument()
    {
        $this->expectException(InvalidReportTypeException::class);

        $this->instance->getReportTypeUid('bla bla');
    }

    /**
     * Тест метода `getReport()` с не корректным типом.
     */
    public function testGetReport()
    {
        $this->assertInstanceOf(ReportData::class, $this->instance->getReport(
            'default'
        ));
    }

    /**
     * Тест метода `refreshReport()` с не корректным типом.
     */
    public function testRefreshReport()
    {
        $this->assertInstanceOf(ReportStatusData::class, $this->instance->refreshReport(
            'default'
        ));
    }

    /**
     * Тест метода `generateWebHooksOptions()`.
     */
    public function testGenerateWebHooksOptions()
    {
        $this->assertEquals(['webhook' => []], $this->callMethod($this->instance, 'generateWebHooksOptions'));

        $this->app->make('config')->set(
            'b2b-api-client.webhooks.on.complete',
            $on_complete = 'http://google.com/hook?foo=bar&bar=baz1'
        );

        $this->app->make('config')->set(
            'b2b-api-client.webhooks.on.update',
            $on_update = 'http://yandex.ru/hook?foo=bar1&bar=baz'
        );

        $this->assertEquals(['webhook' => [
            'on_complete' => $on_complete,
            'on_update'   => $on_update,
        ]], $this->callMethod($this->instance, 'generateWebHooksOptions'));
    }
}
