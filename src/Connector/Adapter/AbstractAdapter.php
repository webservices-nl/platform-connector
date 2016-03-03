<?php

namespace Webservicesnl\Connector\Adapter;

abstract class AbstractAdapter
{
    const PROTOCOL_NAME = 'abstract';

    /**
     * @var array
     */
    protected $settings;

    public function getProtocol()
    {
        return static::PROTOCOL_NAME;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
