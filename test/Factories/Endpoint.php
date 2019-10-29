<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Endpoint\Endpoint;

FactoryMuffin::define(Endpoint::class, [
    'lastConnected' => 'dateTime',
    'status'        => Endpoint::STATUS_DISABLED,
    'url'           => 'url',
]);
