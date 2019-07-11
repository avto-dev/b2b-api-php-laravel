<?php

namespace AvtoDev\B2BApiLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use AvtoDev\B2BApiLaravel\B2BApiService;
use AvtoDev\B2BApi\References\QueryTypes;
use AvtoDev\B2BApi\Clients\v1\Client as B2BApiClient;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportData;
use AvtoDev\B2BApi\Responses\DataTypes\Report\ReportStatusData;

/**
 * Class B2BApiServiceFacade.
 *
 * @method static B2BApiService instance()
 * @method static B2BApiClient client()
 * @method static QueryTypes queryTypes()
 * @method static ReportStatusData makeReport($query_type, $query_id, $report_type)
 * @method static ReportData getReport($report_uid)
 * @method static ReportStatusData refreshReport($report_uid)
 * @method static string generateAuthToken()
 * @method static null|string getReportTypeUid()
 *
 * @see B2BApiService
 *
 * @deprecated This package is abandoned. Migrate to the package: <https://github.com/avtocod/b2b-api-php-laravel>
 */
class B2BApiServiceFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'b2b-api.service';
    }
}
