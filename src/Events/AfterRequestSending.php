<?php

namespace AvtoDev\B2BApiLaravel\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * @deprecated This package is abandoned. Migrate to the package: <https://github.com/avtocod/b2b-api-php-laravel>
 */
class AfterRequestSending extends AbstractRequestEvent
{
    /**
     * @var ResponseInterface
     */
    public $response;

    /**
     * AfterRequestSending constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
