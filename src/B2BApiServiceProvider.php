<?php

namespace AvtoDev\B2BApiLaravel;

use Illuminate\Foundation\Application;
use Psr\Http\Message\ResponseInterface;
use AvtoDev\B2BApiLaravel\Events\AfterRequestSending;
use AvtoDev\B2BApiLaravel\Events\BeforeRequestSending;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class B2BApiServiceProvider extends IlluminateServiceProvider
{
    /**
     * Возвращает путь до файла-конфигурации пакета (данный метод можно переопределять).
     *
     * @return string
     */
    public static function getConfigFilePath()
    {
        return __DIR__ . '/../config/b2b-api-client.php';
    }

    /**
     * Возвращает путь до файла-конфигурации текущего пакета (не переопределяемый).
     *
     * @return string
     */
    private static function getBasicConfigFilePath()
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
        return \basename(static::getConfigFilePath(), '.php');
    }

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
     * Выполнение после-регистрационной загрузки сервисов.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            \realpath($config_path = static::getConfigFilePath()) => config_path(\basename($config_path)),
        ], 'config');
    }

    /**
     * Initialize configs.
     *
     * @return void
     */
    protected function initializeConfigs()
    {
        $package = static::getConfigFilePath();
        $basic   = static::getBasicConfigFilePath();
        $root    = static::getConfigRootKeyName();

        $this->mergeConfigFrom($basic, $root);

        if ($basic !== $package) {
            $this->mergeConfigFrom($package, $root);
        }
    }

    /**
     * Регистрирует контейнер-коллекцию типов отчетов.
     *
     * @return void
     */
    protected function registerReportTypesRepository()
    {
        $this->app->singleton(ReportTypesRepository::class, function (Application $app) {
            /** @var ConfigRepository $config */
            $config = $app->make('config');

            $repository = new ReportTypesRepository($config->get(
                sprintf('%s.report_types.uids', static::getConfigRootKeyName())
            ));

            if ($repository->hasName($name = $config->get(sprintf(
                '%s.report_types.use_as_default', static::getConfigRootKeyName()
            )))) {
                $repository->setDefaultReportType($repository->getByName($name));
            }

            return $repository;
        });

        $this->app->bind('b2b-api.report-types.repository', ReportTypesRepository::class);
    }

    /**
     * Регистрирует контейнер-реализацию клиента.
     *
     * @return void
     */
    protected function registerClientInstance()
    {
        $this->app->singleton(B2BApiService::class, function (Application $app) {
            /** @var ConfigRepository $config */
            $config = $app->make('config');

            $client = new B2BApiService($app, $config->get(static::getConfigRootKeyName()));

            // "Вешаем" на события внутри клиента Laravel-обработчики
            $client->client()->httpClient()->on('before_request',
                function (&$method, &$uri, &$data, &$headers) {
                    event(new BeforeRequestSending($method, $uri, $data, $headers));
                }
            );
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
