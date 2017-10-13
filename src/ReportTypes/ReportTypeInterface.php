<?php

namespace AvtoDev\B2BApiLaravel\ReportTypes;

use Traversable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface ReportTypeInterface.
 */
interface ReportTypeInterface extends Arrayable, Jsonable
{
    /**
     * Устанавливает UID типа отчета.
     *
     * @param string|null $uid
     *
     * @return static|self
     */
    public function setUid($uid);

    /**
     * Возвращает UID типа отчета.
     *
     * @return null|string
     */
    public function getUid();

    /**
     * Устанавливает описание типа отчета.
     *
     * @param string|null $description
     *
     * @return static|self
     */
    public function setDescription($description);

    /**
     * Возвращает описание типа отчета.
     *
     * @return null|string
     */
    public function getDescription();

    /**
     * Устанавливает имя типа отчета.
     *
     * @param string|null $name
     *
     * @return static|self
     */
    public function setName($name);

    /**
     * Возвращает имя типа отчета.
     *
     * @return null|string
     */
    public function getName();

    /**
     * Производит конфигурацию своих значений в зависимости от типа входящего значения.
     *
     * @param string|array|Traversable|ReportTypeInterface $input
     */
    public function configure($input);
}
