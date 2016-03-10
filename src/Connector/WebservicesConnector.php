<?php

namespace Webservicesnl\Connector;

/**
 * Class WebservicesConnector.
 *
 * Proxy class for calling everything Webservices regardless of the underlying protocol
 *
 * This file is regenerated with each release ...
 */
class WebservicesConnector extends AbstractConnector
{
    const PLATFORM_NAME = 'webservices';

    /**
     * This is just demo function
     *
     * @param string $username
     *
     * @return mixed
     */
    public function getUser($username)
    {
        return $this->getAdapter()->call('getUser', ['username', $username]);
    }
}
