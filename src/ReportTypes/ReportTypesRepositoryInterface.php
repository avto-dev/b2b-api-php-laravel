<?php

namespace AvtoDev\B2BApiLaravel\ReportTypes;

/**
 * Interface ReportTypesRepositoryInterface.
 */
interface ReportTypesRepositoryInterface
{
    /**
     * Устанавливает ReportType, используемый по умолчанию.
     *
     * @param ReportType|string|array $report_type
     *
     * @return void
     */
    public function setDefaultReportType($report_type);

    /**
     * Возвращает установленный ранее ReportType, который используется по умолчанию.
     *
     * @return ReportType|null
     */
    public function getDefaultReportType();

    /**
     * Возвращает объект типа отчета по его имени.
     *
     * @param string $report_type_name
     *
     * @return ReportTypeInterface|null
     */
    public function getByName($report_type_name);

    /**
     * Проверяет наличие объекта типа отчета по его имени.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasName($name);

    /**
     * Возвращает массив всех имен типов отчетов.
     *
     * @return string[]|array
     */
    public function getAllNames();

    /**
     * Возвращает объект типа отчета по его uid-у.
     *
     * @param string $report_type_uid
     *
     * @return ReportTypeInterface|null
     */
    public function getByUid($report_type_uid);

    /**
     * Проверяет наличие объекта типа отчета по UID.
     *
     * @param string $uid
     *
     * @return bool
     */
    public function hasUid($uid);

    /**
     * Возвращает массив всех UIDов типов отчетов.
     *
     * @return string[]|array
     */
    public function getAllUids();

    /**
     * Конвертирует переданное методу значение в объект типа ReportType, если это возможно.
     *
     * @param string|array|array[]|object $some_value
     *
     * @return ReportType|null
     */
    public function toReportType($some_value);
}
