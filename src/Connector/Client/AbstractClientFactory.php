<?php

namespace WebservicesNl\Connector\Client;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * AbstractClientFactory
 *
 * Base ClientFactory.
 */
abstract class AbstractClientFactory implements ClientFactoryInterface
{
    use LoggerAwareTrait;

    /**
     * Returns whether this instance is blessed with a LoggerInterface.
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->logger instanceof LoggerInterface;
    }
}
