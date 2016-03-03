<?php

namespace Webservicesnl\Connector;

/**
 * Class WebservicesConnector.
 *
 * Proxy class for calling everything Webservices regardless of the underlying protocol
 *
 * This file is regenerated with each release ...
 */
class WebservicesConnector extends BaseConnector
{
    /**
     * @param string $username
     *
     * @return mixed
     */
    public function getUser($username)
    {
        return $this->getAdapter()->call('getUser', ['username', $username]);
    }
}
