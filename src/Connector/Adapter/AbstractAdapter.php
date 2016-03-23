<?php

namespace WebservicesNl\Connector\Adapter;

/**
 * Class AbstractAdapter.
 */
abstract class AbstractAdapter
{
    const PROTOCOL_NAME = 'abstract';

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return static::PROTOCOL_NAME;
    }
}
