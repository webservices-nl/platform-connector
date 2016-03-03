<?php

namespace Webservicesnl\Connector\Adapter;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class RestAdapter
 * @package Connector\Adapter
 */
class RestAdapter extends AbstractAdapter implements AdapterInterface
{
    const PROTOCOL_NAME = 'rest';

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * SoapAdapter constructor.
     *
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function call($functionName, $arguments)
    {

    }
}