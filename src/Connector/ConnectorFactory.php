<?php

namespace Webservicesnl\Connector;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Webservicesnl\Exception\Client\Input\InvalidException;
use Webservicesnl\Exception\Client\InputException;
use Webservicesnl\Soap\Client\SoapFactory;

/**
 * Class ConnectorFactory.
 *
 * Helps with creating a connector for a given platform over a certain protocol.
 *
 * @package Webservicesnl\Connector
 */
class ConnectorFactory implements LoggerAwareInterface
{
    // add logger interface
    use LoggerAwareTrait;

    const PLATFORM_WS = 'webservices';
    const PLATFORM_RDW = 'rdw';

    /**
     * @var array
     */
    public static $platforms = [
        self::PLATFORM_RDW,
        self::PLATFORM_WS,
    ];

    /**
     * Creates an connection for a given platform
     *
     * @param string               $platform
     * @param string               $protocol
     * @param array                $settings
     * @param LoggerInterface|null $logger
     *
     * @return SoapFactory
     * @throws InputException
     */
    public static function create($platform, $protocol, array $settings = [], LoggerInterface $logger = null)
    {
        // check input
        if (!in_array($platform, self::$platforms)) {
            throw new InputException('Not a valid platform');
        }

        $platform = ucfirst($platform);
        $protocol = ucfirst($protocol);

        $clientFactory = sprintf("Webservicesnl\\%s\\Client\\%sFactory", $protocol, $protocol);
        $connectorFQCN = sprintf(__NAMESPACE__ .'\\'. $platform . 'Connector');
        $adapterFQCN = sprintf(__NAMESPACE__ .'\\Adapter\\'. $protocol . 'Adapter');

        if (!class_exists($clientFactory) || !class_exists($connectorFQCN)) {
            throw new InputException("Could not load classes for '$protocol' and '$platform'");
        }

        // Try to create protocol client (like a SoapClient or RestClient)
        $client = $clientFactory::build($platform, $logger)->create($settings);
        $adapter = new $adapterFQCN($client);

        $connector = new $connectorFQCN($adapter, $settings);

        return $connector;
    }
}
