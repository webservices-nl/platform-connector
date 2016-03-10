<?php

namespace Webservicesnl\Connector;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Webservicesnl\Common\Client\ClientFactoryInterface;
use Webservicesnl\Common\Exception\Client\InputException;
use Webservicesnl\Connector\Adapter\AdapterInterface;

/**
 * Class ConnectorFactory.
 *
 * Helps with creating a connector for a given platform over a certain protocol.
 * Provide some user settings and afterwards create platforms like a boss
 */
class ConnectorFactory implements LoggerAwareInterface
{
    // add logger interface
    use LoggerAwareTrait;

    const PLATFORM_WS = 'webservices';
    const PLATFORM_RDW = 'rdw';

    /**
     * Available platforms
     *
     * @var array
     */
    public static $platforms = [
        self::PLATFORM_RDW,
        self::PLATFORM_WS,
    ];

    /**
     * Generic settings (eg credentials)
     *
     * @var array
     */
    protected $settings;

    /**
     * ConnectorFactory constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * @param array $settings
     *
     * @return static
     */
    public static function build(array $settings = [])
    {
        return new static($settings);
    }

    /**
     * Creates an connection for a given platform.
     *
     * @param string $platform
     * @param string $protocol
     *
     * @return ConnectorInterface
     * @throws InputException
     */
    public function create($platform, $protocol)
    {
        $platform = ucfirst($platform);
        $protocol = ucfirst($protocol);

        /** @var ClientFactoryInterface $clientFactory */
        $clientFactory = sprintf('Webservicesnl\\%s\\Client\\%sFactory', $protocol, $protocol);

        /** @var ConnectorInterface $connectorFQCN */
        $connectorFQCN = sprintf(__NAMESPACE__ . '\\' . $platform . 'Connector');

        /** @var AdapterInterface $adapterFQCN */
        $adapterFQCN = sprintf(__NAMESPACE__ . '\\Adapter\\' . $protocol . 'Adapter');

        if (!class_exists($clientFactory) || !class_exists($connectorFQCN)) {
            throw new InputException("Could not load classes for '$platform' and '$protocol'");
        }

        // try to create protocol client (like a SoapClient or RestClient)
        $client = $clientFactory::build($platform, $this->getLogger())->create($this->settings);
        $adapter = new $adapterFQCN($client);

        return new $connectorFQCN($adapter, $this->settings);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
