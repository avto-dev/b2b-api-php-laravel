<?php

namespace AvtoDev\B2BApiLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportType;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;

/**
 * Class ReportTypesRepositoryFacade.
 *
 * @method static ReportTypesRepository instance()
 * @method static ReportTypeInterface|null getByName(string $report_type_name)
 * @method static ReportTypeInterface|null getByUid(string $report_type_uid)
 * @method static string[]|array getAllNames()
 * @method static string[]|array getAllUids()
 * @method static void setDefaultReportType(ReportType|string|array $report_type)
 * @method static ReportType|null getDefaultReportType()
 * @method static bool hasName()
 * @method static bool hasUid()
 * @method static ReportType|null toReportType(string|array|array[]|object $some_value)
 *
 * @see ReportTypesRepository
 * @deprecated This package is abandoned. Migrate to the package: <https://github.com/avtocod/b2b-api-php-laravel>
 */
class ReportTypesRepositoryFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'b2b-api.report-types.repository';
    }
}
