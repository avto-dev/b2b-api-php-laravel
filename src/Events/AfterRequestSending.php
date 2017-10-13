<?php

namespace AvtoDev\B2BApiLaravel\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AfterRequestSending.
 */
class AfterRequestSending extends AbstractRequestEvent
{
    /**
     * @var ResponseInterface|null
     */
    public $response;

    /**
     * AfterRequestSending constructor.
     *
     * @param ResponseInterface|null $response
     */
    public function __construct($response)
    {
        $this->response = $response instanceof ResponseInterface ? $response : null;
    }
}
