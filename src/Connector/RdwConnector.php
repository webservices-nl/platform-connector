<?php

namespace Webservicesnl\Connector;

/**
 * Class RwdConnector
 */
class RdwConnector extends BaseConnector
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
