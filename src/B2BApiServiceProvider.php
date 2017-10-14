<?php

namespace AvtoDev\B2BApiLaravel;

use Illuminate\Foundation\Application;
use Psr\Http\Message\ResponseInterface;
use AvtoDev\B2BApiLaravel\Events\AfterRequestSending;
use AvtoDev\B2BApiLaravel\Events\BeforeRequestSending;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class B2BApiServiceProvider.
 *
 * Сервис-провайдер пакета, реализующего работу с сервисом B2B API.
 */
class B2BApiServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->initializeConfigs();

        $this->registerReportTypesRepository();
        $this->registerClientInstance();
    }

    /**
     * Возвращает путь до файла-конфигурации пакета.
     *
     * @return string
     */
    public static function getConfigFilePath()
    {
        return __DIR__ . '/../config/b2b-api-client.php';
    }

    /**
     * Get config root key name.
     *
     * @return string
     */
    public static function getConfigRootKeyName()
    {
        return basename(static::getConfigFilePath(), '.php'); // 'b2b-api-client'
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs()
    {
        $this->mergeConfigFrom(static::getConfigFilePath(), static::getConfigRootKeyName());
    }

    /**
     * Регистрирует контейнер-коллекцию типов отчетов.
     *
     * @return void
     */
    protected function registerReportTypesRepository()
    {
        $this->app->singleton(ReportTypesRepository::class, function () {
            return new ReportTypesRepository($this->config()->get(
                sprintf('%s.report_types.uids', static::getConfigRootKeyName())
            ));
        });

        $this->app->bind('b2b-api.report-types.repository', ReportTypesRepository::class);
    }

    /**
     * Возвращает контейнер с конфигурацией приложения.
     *
     * @return ConfigRepository
     */
    protected function config()
    {
        return $this->app->make('config');
    }

    /**
     * Выполнение после-регистрационной загрузки сервисов.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath($config_path = static::getConfigFilePath()) => config_path(basename($config_path)),
        ], 'config');
    }

    /**
     * Регистрирует контейнер-реализацию клиента.
     *
     * @return void
     */
    protected function registerClientInstance()
    {
        $this->app->singleton(B2BApiService::class, function (Application $app) {
            $client = new B2BApiService($app, $this->config()->get(static::getConfigRootKeyName()));

            // "Вешаем" на события внутри клиента Laravel-обработчики
            $client->client()->httpClient()->on('before_request',
                function (&$method, &$uri, &$data, &$headers) {
                    event(new BeforeRequestSending($method, $uri, $data, $headers));
                });
            $client->client()->httpClient()->on('after_request',
                function (ResponseInterface $response) {
                    event(new AfterRequestSending(clone $response));
                }
            );

            return $client;
        });

        $this->app->bind('b2b-api.service', B2BApiService::class);
    }
}
