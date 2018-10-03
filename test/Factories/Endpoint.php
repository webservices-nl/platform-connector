<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Endpoint\Endpoint;

FactoryMuffin::define('WebservicesNl\Common\Endpoint\Endpoint', [
    'lastConnected' => 'dateTime',
    'status' => Endpoint::STATUS_DISABLED,
    'url' => 'url',
]);
