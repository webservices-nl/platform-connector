<?php

namespace WebservicesNl\Connector\ProtocolAdapter;

use WebservicesNl\Common\Exception\Server\NoServerAvailableException;
use WebservicesNl\Connector\Client\ClientInterface;
use WebservicesNl\Protocol\Soap\Client\SoapClient;

/**
 * SoapAdapter.
 *
 * Soap protocol adapter for a ConnectorInterface to connect to the Webservices API.
 */
class SoapAdapter extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     *
     * @param string $functionName name of the function call
     * @param mixed  $args         arguments for the function call
     *
     * @throws NoServerAvailableException
     * @throws \SoapFault
     * @throws \Exception
     *
     * @return mixed
     */
    public function call($functionName, $args)
    {
        if (method_exists($this->getClient(), $functionName) === true) {
            // @codeCoverageIgnoreStart
            return $this->getClient()->$functionName($args);
            // @codeCoverageIgnoreEnd
        }

        return $this->getClient()->__soapCall($functionName, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @return SoapClient|ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
