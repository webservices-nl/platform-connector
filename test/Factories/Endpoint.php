<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use Webservicesnl\Common\Endpoint\Endpoint;

FactoryMuffin::define('Webservicesnl\Common\Endpoint\Endpoint', [
    'lastConnected' => 'dateTime',
    'status'        => Endpoint::STATUS_DISABLED,
    'url'           => 'url',
]);
