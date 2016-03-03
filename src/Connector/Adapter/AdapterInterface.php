<?php

namespace Webservicesnl\Connector\Adapter;

interface AdapterInterface
{
    /**
     *
     * @param string $functionName
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function call($functionName, $arguments);
}