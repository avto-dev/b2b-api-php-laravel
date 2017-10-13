<?php

namespace AvtoDev\B2BApiLaravel\Facades;

use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypeInterface;
use AvtoDev\B2BApiLaravel\ReportTypes\ReportTypesRepository;
use Illuminate\Support\Facades\Facade;

/**
 * Class ReportTypesRepositoryFacade.
 *
 * @method static ReportTypesRepository instance()
 * @method static ReportTypeInterface|null getByName(string $report_type_name)
 * @method static ReportTypeInterface|null getByUid(string $report_type_uid)
 * @method static string[]|array getAllNames()
 * @method static string[]|array getAllUids()
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
