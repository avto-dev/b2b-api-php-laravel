<?php

namespace AvtoDev\B2BApiLaravel\ReportTypes;

use Traversable;
use Illuminate\Support\Collection;
use AvtoDev\B2BApiLaravel\Traits\InstanceableTrait;

/**
 * Class ReportTypesRepository.
 *
 * Коллекция (репозиторий) всех типов отчетов.
 */
class ReportTypesRepository extends Collection
{
    use InstanceableTrait;

    /**
     * Стек объектов - типов отчетов.
     *
     * @var ReportType[]|array
     */
    protected $items = [];

    /**
     * Тип отчета, используемый по умолчанию.
     *
     * @var ReportType|null
     */
    protected $default_report_type;

    /**
     * ReportTypesRepository constructor.
     *
     * @param array|Traversable|mixed $items
     */
    public function __construct($items = [])
    {
        $this->items = $this->toArrayOfReportTypes($items);
    }

    /**
     * Устанавливает ReportType, используемый по умолчанию.
     *
     * @param ReportType|string|array $report_type
     *
     * @return void
     */
    public function setDefaultReportType($report_type)
    {
        $report_type = $this->toReportType($report_type);

        if ($report_type instanceof ReportType) {
            $this->default_report_type = $report_type;
        }
    }

    /**
     * Возвращает установленный ранее ReportType, который используется по умолчанию.
     *
     * @return ReportType|null
     */
    public function getDefaultReportType()
    {
        return $this->default_report_type;
    }

    /**
     * Возвращает объект типа отчета по его имени.
     *
     * @param string $report_type_name
     *
     * @return ReportTypeInterface|null
     */
    public function getByName($report_type_name)
    {
        foreach ($this->all() as $item) {
            if ($item instanceof ReportTypeInterface && $item->getName() === $report_type_name) {
                return $item;
            }
        }
    }

    /**
     * Проверяет наличие объекта типа отчета по его имени.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasName($name)
    {
        return in_array($name, $this->getAllNames());
    }

    /**
     * Возвращает массив всех имен типов отчетов.
     *
     * @return string[]|array
     */
    public function getAllNames()
    {
        $result = [];

        foreach ($this->all() as $item) {
            if ($item instanceof ReportTypeInterface && ! empty($name = $item->getName())) {
                array_push($result, $name);
            }
        }

        return $result;
    }

    /**
     * Возвращает объект типа отчета по его uid-у.
     *
     * @param string $report_type_uid
     *
     * @return ReportTypeInterface|null
     */
    public function getByUid($report_type_uid)
    {
        foreach ($this->all() as $item) {
            if ($item instanceof ReportTypeInterface && $item->getUid() === $report_type_uid) {
                return $item;
            }
        }
    }

    /**
     * Проверяет наличие объекта типа отчета по UID.
     *
     * @param string $uid
     *
     * @return bool
     */
    public function hasUid($uid)
    {
        return in_array($uid, $this->getAllUids());
    }

    /**
     * Возвращает массив всех UIDов типов отчетов.
     *
     * @return string[]|array
     */
    public function getAllUids()
    {
        $result = [];

        foreach ($this->all() as $item) {
            if ($item instanceof ReportTypeInterface && ! empty($uid = $item->getUid())) {
                array_push($result, $uid);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        // Пытаемся преобразовать значение в объект типа ReportType, если в этом есть необходимость
        $value = $value instanceof ReportTypeInterface
            ? $value
            : $this->toReportType($value);

        if ($value instanceof ReportTypeInterface) {
            if (is_null($key)) {
                array_push($this->items, $value);
            } else {
                $this->items[$key] = $value;
            }
        }
    }

    /**
     * Преобразует входящее значение в массив объектов типа ReportType.
     *
     * Принимает на вход массив данных о типах отчетов, и в их соответствии формирует коллекцию данных. Формат
     * массивов может быть следующим:
     *
     * <code>
     * new ReportType('uid_0')
     *
     * 'uid_0'
     *
     * 'uid_0,uid_1'
     *
     * [
     *   'uid_1',
     *   'uid_2',
     * ]
     *
     * [
     *   'uid_1' => ['uid' => 'report_1_uid', 'description' => 'Some 1 description'],
     *   'uid_2' => ['id' => 'report_2_uid',  'desc' => 'Some 2 description'],
     *  ['name' => 'some_3_name', 'id' => 'report_3_uid', 'description' => 'Some 3 description'],
     * ]
     * </code>
     *
     * @param string|array|array[]|object $items
     *
     * @return ReportType[]|array
     */
    protected function toArrayOfReportTypes($items)
    {
        $result = [];

        // Если влетело некоторое скалярное выражение - то преобразуем его в массив по разделителю
        if (is_scalar($items) && ! empty($items)) {
            $items = explode(',', trim((string) $items));
        }

        // Если элементом является объект, умеющим себя преобразовывать в массив - то преобразуем
        if (is_object($items) && ! ($items instanceof ReportType) && method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        foreach ((array) $items as $key => $value) {
            if ($value instanceof ReportType) {
                // Если элементом уже является объект типа ReportType - то сразу его пушим в стек
                array_push($result, $value);
            } elseif (is_scalar($value) && ! empty($value)) {
                // Если влетела строка - то пушим в стек объект, у которого и имя, и UID равны этой строке
                $report_type = new ReportType;
                $report_type->setUid($value);
                $report_type->setName($value);

                array_push($result, $report_type);
            } elseif (is_array($value) || $value instanceof Traversable) {
                // Если массив или перебираемое дерьмо - то работаем с ним
                $report_type = new ReportType;
                $report_type->setName($key);
                $report_type->configure($value);

                array_push($result, $report_type);
            }
        }

        return $result;
    }

    /**
     * Конвертирует переданное методу значение в объект типа ReportType, если это возможно.
     *
     * @param string|array|array[]|object $some_value
     *
     * @return ReportType|mixed|null
     */
    public function toReportType($some_value)
    {
        $report_types_array = $this->toArrayOfReportTypes($some_value);

        return isset($report_types_array[0]) && (($report_type = $report_types_array[0]) instanceof ReportType)
            ? $report_type
            : null;
    }
}
