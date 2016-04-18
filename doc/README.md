# Webservices PHP Connector

All services offered by Webservices.nl are available as methods in this class.
The methods __getServerState and __setServerState can be used to prevent timeouts when a server is unavailable.

All parameters are expected to be in UTF-8 encoding, output is in UTF-8 as well. For documentation see: 
https://ws1.webservices.nl/documentation

## Prerequisites:
- PHP: 5.4+
- composer (https://getcomposer.org)

## Install
Please composer to install this library.

```bash 
composer require webservices-nl/soap-client 
```

## Usage
```php
 
 // create a soap connector to webservices
 $soap = ConnectorFactory::build(['username' => 'myusername', 'password' => 'secret'])->create('soap', 'webservices');
              
 // create a xmlrpc connector to webservices
 $rpc = ConnectorFactory::build(['username' => 'myusername', 'password' => 'secret'])->create('xmlrpc', 'webservices');

 // create a soap connector to KvK
 $rpcConnector = ConnectorFactory::build(['username' => 'myusername', 'password' => 'secret'])->create('soap', 'kvk');

```

## Run tests?
This client has been tested on PHP 5.4, 5.5, 5.6 and 7.0

```bash

composer install --dev 
phpunit

```

Any questions, remarks, bugs? Please mail us.
- technical questions: <mailto:tech@webservices.nl>
- support questions: <mailto:support.webservices.nl>