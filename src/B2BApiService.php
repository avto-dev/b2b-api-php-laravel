<?php

namespace AvtoDev\B2BApiLaravel;

use AvtoDev\B2BApi\Tokens\AuthToken;
use AvtoDev\B2BApi\Clients\v1\Client;
use Illuminate\Foundation\Application;
use AvtoDev\B2BApi\References\QueryTypes;
use AvtoDev\B2BApi\Exceptions\B2BApiException;
use AvtoDev\B2BApiLaravel\Traits\InstanceableTrait;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportData;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\Exceptions\B2BApiServiceException;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;
use AvtoDev\B2BApi\Exceptions\B2BApiInvalidArgumentException;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportStatusData;
use AvtoDev\B2BApiLaravel\Exceptions\InvalidReportTypeException;

/**
 * Клиент, реализующий работу с B2B API.
 */
class B2BApiService
{
    use InstanceableTrait;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var AuthToken
     */
    protected $auth_token;

    /**
     * @var ReportTypesRepository
     */
    protected $report_types_repository;

    /**
     * @var QueryTypes
     */
    protected $query_types;

    /**
     * Конфиг (должен соответствовать структуре конфига пакета).
     *
     * Описываем значения полностью для полноценной работы type-hint.
     *
     * @var array
     */
    protected $config = [
        'api_base_uri' => null,
        'domain'       => null,
        'username'     => null,
        'password'     => null,
        'is_test'      => false,
    ];

    /**
     * B2BApiService constructor.
     *
     * @param Application $app
     * @param array       $config
     */
    public function __construct(Application $app, $config = [])
    {
        $this->app                     = $app;
        $this->config                  = \array_replace_recursive($this->config, (array) $config);
        $this->auth_token              = new AuthToken;
        $this->query_types             = new QueryTypes;
        $this->report_types_repository = $app->make(ReportTypesRepository::class);

        // И инициализируем клиента B2B API
        $this->client = new Client([
            'api' => [
                'versions' => [
                    'default' => [
                        'base_uri' => $this->config['api_base_uri'],
                    ],
                ],
            ],

            'use_api_version' => 'default',

            'is_test' => $this->config['is_test'],
        ]);
    }

    /**
     * Возвращает инстанс клиента B2B API.
     *
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Возвращает справочник типов идентификаторов запросов.
     *
     * @return QueryTypes
     */
    public function queryTypes()
    {
        return $this->query_types;
    }

    /**
     * Создает (заказывает) отчет у сервиса B2B API.
     *
     * Если методу не передать значение $report_type - то он будет автоматически извлечен из конфигурации.
     *
     * @param string                          $query_type  Тип идентификатора запрашиваемой сущности
     * @param string                          $query_id    Идентификатор запрашиваемой сущности (VIN, GRZ и так далее)
     * @param ReportTypeInterface|string|null $report_type UID типа отчета либо его **имя** (из конфигурации)
     * @param bool                            $is_force    Нужно ли перегенерировать отчет если он уже существует?
     *
     * @throws B2BApiServiceException
     * @throws B2BApiInvalidArgumentException
     * @throws B2BApiException
     *
     * @return ReportStatusData
     */
    public function makeReport($query_type, $query_id, $report_type = null, $is_force = false)
    {
        // Если методу передан пустой $report_type, то пытаемся извлечь ReportType из репозитория, который установлен
        // по как используемый по умолчанию
        if ($report_type === null) {
            $default = $this->report_types_repository->getDefaultReportType();

            if ($default instanceof ReportTypeInterface) {
                $report_type = $default;
            }
        }

        $response = $this->client->user()->report()->make(
            $this->generateAuthToken(),
            $query_type,
            $query_id,
            $this->getReportTypeUid($report_type),
            $is_force
        );

        if (($status = $response->data()->first()) && $status instanceof ReportStatusData) {
            return $status;
        }

        throw new B2BApiServiceException(sprintf('Invalid response type: "%s"', get_class($status)));
    }

    /**
     * Генерирует новый токен авторизации на сервисе B2B API.
     *
     * @param int      $age       Время жизни токена (unix-time, в секундах)
     * @param int|null $timestamp Временная метка (unix-time, начала жизни токена)
     *
     * @return string
     */
    public function generateAuthToken($age = 172800, $timestamp = null)
    {
        $auth_token = $this->auth_token; // PHP 5.6

        return $auth_token::generate(
            $this->config['username'],
            $this->config['password'],
            $this->config['domain'],
            $age,
            $timestamp
        );
    }

    /**
     * Возвращает UID ипа отчета по переданной сущностью. В роли сущности может выступать как объект типа отчета, так и
     * строковое значение любого известного имени типа отчета, либо непосредственно его UID.
     *
     * @param ReportTypeInterface|string $input
     *
     * @throws InvalidReportTypeException
     *
     * @return null|string
     */
    public function getReportTypeUid($input)
    {
        if ($input instanceof ReportTypeInterface) {
            return $input->getUid();
        }

        if (is_string($input) && ! empty($input)) {
            $report_type = $this->report_types_repository->getByName($input);

            if ($report_type instanceof ReportTypeInterface) {
                return $report_type->getUid();
            }

            $report_type = $this->report_types_repository->getByUid($input);

            if ($report_type instanceof ReportTypeInterface) {
                return $report_type->getUid();
            }
        }

        throw new InvalidReportTypeException(sprintf('Invalid report type identifier: "%s"', $input));
    }

    /**
     * Возвращает объект отчета B2B API.
     *
     * @param string $report_uid UID отчета
     *
     * @throws B2BApiServiceException
     * @throws B2BApiException
     *
     * @return ReportData
     */
    public function getReport($report_uid)
    {
        $response = $this->client->user()->report()->get($this->generateAuthToken(), $report_uid);

        if (($report = $response->data()->first()) && $report instanceof ReportData) {
            return $report;
        }

        throw new B2BApiServiceException(sprintf('Invalid response type: "%s"', get_class($report)));
    }

    /**
     * Обновляет имеющийся отчет у сервиса B2B API по его UID.
     *
     * @param string $report_uid UID отчета
     *
     * @throws B2BApiServiceException
     * @throws B2BApiException
     *
     * @return ReportStatusData
     */
    public function refreshReport($report_uid)
    {
        $response = $this->client->user()->report()->refresh($this->generateAuthToken(), $report_uid);

        if (($report = $response->data()->first()) && $report instanceof ReportStatusData) {
            return $report;
        }

        throw new B2BApiServiceException(sprintf('Invalid response type: "%s"', get_class($report)));
    }
}
