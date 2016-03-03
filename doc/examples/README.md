# Webservices PHP Connector

All services offered by Webservices.nl are available as methods in this class.
The methods __getServerState and __setServerState can be used to prevent timeouts when a server is unavailable.

All parameters are expected to be in UTF-8 encoding, output is in UTF-8 as well. For documentation see: 
https://ws1.webservices.nl/documentation

## Prerequisites:
- PHP: 5.4+
- composer

## Install
``` composer require webservices-nl/soap-client  ```

## Run tests?
This client has been tested on PHP 5.4, 5.5, 5.6 and 7.0
``` phpunit ```

Any questions, remarks, bugs?
    - tech@webservices.nl
    - support.webservices.nl