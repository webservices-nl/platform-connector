<?php

namespace Webservicesnl\Connector\Adapter;

use Webservicesnl\Soap\Client\SoapClient;

/**
 * Class SoapAdapter
 * @package Webservicesnl\Connector\Adapter
 */
class SoapAdapter extends AbstractAdapter implements AdapterInterface
{
    const PROTOCOL_NAME = 'soap';

    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * SoapAdapter constructor.
     *
     * @param SoapClient $client
     */
    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    public function call($functionName, $args)
    {
        if (in_array($functionName, get_class_methods('SoapClient'))) {
            return call_user_func(array($this->client, $functionName), $args);
        }

        return $this->client->soapCall($functionName, $args);
    }
}
