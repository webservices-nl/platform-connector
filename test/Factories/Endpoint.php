<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

use Webservicesnl\Endpoint\Endpoint;

FactoryMuffin::define('Webservicesnl\Endpoint\Endpoint', [
    'lastConnected' => 'dateTime',
    'status'        => Endpoint::STATUS_DISABLED,
    'url'           => 'url',
]);
