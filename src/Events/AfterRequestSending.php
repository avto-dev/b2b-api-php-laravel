<?php

namespace AvtoDev\B2BApiLaravel\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AfterRequestSending.
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
