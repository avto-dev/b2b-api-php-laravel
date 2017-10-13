<?php

namespace AvtoDev\B2BApiLaravel\Tests;

use AvtoDev\B2BApiLaravel\B2BApiService;
use AvtoDev\B2BApiLaravel\Events\AfterRequestSending;
use AvtoDev\B2BApiLaravel\Events\BeforeRequestSending;
use AvtoDev\B2BApiLaravel\Facades\B2BApiServiceFacade;
use AvtoDev\B2BApiLaravel\B2BApiServiceProvider;
use AvtoDev\B2BApiLaravel\Facades\ReportTypesRepositoryFacade;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;

/**
 * Class B2BApiServiceProviderTest.
 */
class B2BApiServiceProviderTest extends AbstractUnitTestCase
{
    /**
     * Tests service-provider loading.
     *
     * @return void
     */
    public function testServiceProviderLoading()
    {
        // Тест контейнера репозитория типов отчетов
        $this->assertInstanceOf(ReportTypesRepository::class, $this->app['b2b-api.report-types.repository']);
        $this->assertInstanceOf(ReportTypesRepository::class, $this->app[ReportTypesRepository::class]);
        $this->assertInstanceOf(ReportTypesRepository::class, app('b2b-api.report-types.repository'));
        $this->assertInstanceOf(ReportTypesRepository::class, app(ReportTypesRepository::class));
        $this->assertInstanceOf(ReportTypesRepository::class, resolve(ReportTypesRepository::class));

        // Тест контейнера сервиса
        $this->assertInstanceOf(B2BApiService::class, $this->app['b2b-api.service']);
        $this->assertInstanceOf(B2BApiService::class, $this->app[B2BApiService::class]);
        $this->assertInstanceOf(B2BApiService::class, app('b2b-api.service'));
        $this->assertInstanceOf(B2BApiService::class, app(B2BApiService::class));
        $this->assertInstanceOf(B2BApiService::class, resolve(B2BApiService::class));
    }

    /**
     * Test accessible from facade.
     *
     * @return void
     */
    public function testAccessibleFromFacade()
    {
        $this->assertInstanceOf(ReportTypesRepository::class, ReportTypesRepositoryFacade::instance());
        $this->assertInstanceOf(B2BApiService::class, B2BApiServiceFacade::instance());
    }

    /**
     * Test default configs values.
     *
     * @return void
     */
    public function testDefaultConfigValues()
    {
        $config = config(B2BApiServiceProvider::getConfigRootKeyName());

        $this->assertTrue(is_string($config['api_base_uri']));
        $this->assertTrue(is_string($config['domain']));
        $this->assertTrue(is_string($config['username']));
        $this->assertTrue(is_string($config['password']));

        $this->assertTrue(is_array($config['report_types']['uids']));
        $this->assertTrue(is_array($config['report_types']['uids']['default']));

        $this->assertEquals('default', $config['report_types']['use_as_default']);

        $this->assertTrue(is_bool($config['is_test']));
    }

    /**
     * Тест контейнера, содержащего объекты типов отчетов.
     *
     * @return void
     */
    public function testReportTypesRepositoryContainer()
    {
        /** @var ReportTypesRepository $container */
        $container = app(ReportTypesRepository::class);

        $this->assertNotEmpty($container->all());
        $this->assertInstanceOf(ReportTypeInterface::class, $container->getByName('default'));
        $this->assertNull($container->getByName('bla bla'));

        $this->assertContains('default', $container->getAllNames());
    }

    /**
     * Тест контейнера сервиса.
     *
     * @return void
     */
    public function testB2BServiceContainer()
    {
        /** @var B2BApiService $container */
        $container = app(B2BApiService::class);

        $this->assertNotEmpty($container->generateAuthToken());
    }

    /**
     * Тест того, что события вызываются успешно.
     *
     * @return void
     */
    public function testEventsFired()
    {
        $this->expectsEvents([
            BeforeRequestSending::class,
            AfterRequestSending::class,
        ]);

        /** @var B2BApiService $service */
        $service = app(B2BApiService::class);

        $service->client()->dev()->ping();
    }
}





















