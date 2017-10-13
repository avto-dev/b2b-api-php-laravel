<?php

namespace AvtoDev\B2BApiLaravel\ReportTypes;

use Traversable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
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
     * ReportTypesRepository constructor.
     *
     * Принимает на вход массив данных о типах отчетов, и в их соответствии формирует коллекцию данных. Формат
     * массивов может быть следующим:
     *
     * <code>
     * 'uid_0'
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
     * @param array|Traversable|mixed $items
     */
    public function __construct($items = [])
    {
        if (is_array($items) || $items instanceof Traversable) {
            foreach ($items as $key => $value) {
                // Если элементом является объект, умеющим себя преобразовывать в массив - то преобразуем
                $value = $value instanceof Arrayable ? $value->toArray() : $value;

                if (is_scalar($value) && ! empty($value)) {
                    // Если влетела строка - то пушим в стек объект, у которого и имя, и UID равны этой строке
                    $this->push(new ReportType([
                        'name' => $value,
                        'uid'  => $value,
                    ]));
                } elseif (is_array($value) || $value instanceof Traversable) {
                    // Если массив или перебираемое дерьмо - то работаем с ним
                    $report_type = new ReportType;
                    $report_type->setName($key);
                    $report_type->configure($value);
                    $this->push($report_type);
                }
            }
        } else {
            parent::__construct($items);
        }
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
}
