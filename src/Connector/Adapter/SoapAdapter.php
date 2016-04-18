<?php

namespace WebservicesNl\Connector\Adapter;

use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Soap\Client\SoapClient;

/**
 * Class SoapAdapter.
 *
 * SoapAdapter for a ConnectInterface
 */
class SoapAdapter extends AbstractAdapter
{
    const PROTOCOL_NAME = 'soap';

    /**
     * {@inheritdoc}
     * @param string $functionName
     * @param mixed  $args
     *
     * @throws NoServerAvailableException
     * @return mixed
     * @throws \SoapFault
     * @throws \Exception
     */
    public function call($functionName, $args)
    {
        if (in_array($functionName, get_class_methods('SoapClient'), false)) {
            return $this->client->{$functionName}($args);
        }

        return $this->getClient()->soapCall($functionName, $args);
    }

    /**
     * @return SoapClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
