<?php

namespace AvtoDev\B2BApiLaravel\ReportTypes;

use Traversable;
use Illuminate\Support\Str;

/**
 * @deprecated This package is abandoned. Migrate to the package: <https://github.com/avtocod/b2b-api-php-laravel>
 */
class ReportType implements ReportTypeInterface
{
    /**
     * UID типа отчета.
     *
     * @var string|null
     */
    protected $uid;

    /**
     * Описание типа отчета.
     *
     * @var string|null
     */
    protected $description;

    /**
     * Имя типа отчета.
     *
     * @var string|null
     */
    protected $name;

    /**
     * ReportType constructor.
     *
     * @param null|string|array|Traversable|ReportTypeInterface $input
     */
    public function __construct($input = null)
    {
        $this->configure($input);
    }

    /**
     * При попытке преобразовать объект в строку - вернется значение его UID-а.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->uid;
    }

    /**
     * {@inheritdoc}
     */
    public function configure($input)
    {
        if ($input instanceof self) {
            $this->setUid($input->getUid());
            $this->setDescription($input->getDescription());
            $this->setName($input->getName());
        } elseif (is_array($input) || $input instanceof Traversable) {
            foreach ($input as $key => $value) {
                switch (Str::lower(trim((string) $key))) {
                    case 'value':
                    case 'id':
                    case 'uid':
                        $this->setUid($value);
                        break;

                    case 'desc':
                    case 'description':
                        $this->setDescription($value);
                        break;

                    case 'name':
                        $this->setName($value);
                        break;
                }
            }
        } elseif (\is_string($input) && ! empty($input)) {
            $this->setUid($input);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return \json_encode($this->toArray(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'uid'         => $this->getUid(),
            'description' => $this->getDescription(),
            'name'        => $this->getName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        $this->uid = $uid === null
            ? null
            : trim((string) $uid);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description === null
            ? null
            : trim((string) $description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name === null
            ? null
            : trim((string) $name);

        return $this;
    }
}
