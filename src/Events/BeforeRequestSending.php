<?php

namespace AvtoDev\B2BApiLaravel\Events;

/**
 * @deprecated This package is abandoned. Migrate to the package: <https://github.com/avtocod/b2b-api-php-laravel>
 */
class BeforeRequestSending extends AbstractRequestEvent
{
    /**
     * @var string|null
     */
    public $method;

    /**
     * @var string|null
     */
    public $uri;

    /**
     * @var array|null
     */
    public $data;

    /**
     * @var array|null
     */
    public $headers;

    /**
     * BeforeRequestSending constructor.
     *
     * @param string|null $method
     * @param string|null $uri
     * @param array|null  $data
     * @param array|null  $headers
     */
    public function __construct(&$method, &$uri, &$data = [], &$headers = [])
    {
        $this->method  = &$method;
        $this->uri     = &$uri;
        $this->data    = &$data;
        $this->headers = &$headers;
    }
}
