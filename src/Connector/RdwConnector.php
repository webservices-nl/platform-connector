<?php

namespace Webservicesnl\Connector;

/**
 * Class RwdConnector
 * @package Webservicesnl\Connector
 */
class RwdConnector extends BaseConnector
{
//    public function getUser($username)
//    {
//        return $this->getAdapter()->getUser($username);
//    }
//
//    public function getPassword()
//    {
//        return $this->getAdapter()->getPassword();
//    }
//
    public function getCarLicense($license)
    {
        return $this->getAdapter()->call('license', $license);
    }
}
