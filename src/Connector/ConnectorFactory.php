<?php

namespace WebservicesNl\Connector;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\Client\ClientFactoryInterface;
use WebservicesNl\Connector\Client\ClientInterface;
use WebservicesNl\Connector\ProtocolAdapter\AdapterInterface;
use WebservicesNl\Platform\PlatformConfigInterface;

/**
 * ConnectorFactory for creating platform connector.
 *
 * Factory for creating a client (connector) for given platform over given certain protocol.
 * Provide user settings for creating new client instances.
 */
class ConnectorFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const PLATFORM_PATH = 'WebservicesNl\\Platform\\%1$s\\PlatformConfig';
    const PROTOCOL_PATH = 'WebservicesNl\\Protocol\\%1$s\\Client\\%1$sFactory';
    const ADAPTER_PATH = 'WebservicesNl\\Connector\\ProtocolAdapter\\%1$sAdapter';

    /**
     * Generic user settings (eg credentials).
     *
     * @var array
     */
    protected $userSettings;

    /**
     * ConnectorFactory constructor.
     *
     * @param array $settings
     * @param LoggerInterface $logger
     */
    public function __construct(array $settings = [], LoggerInterface $logger = null)
    {
        $this->userSettings = $settings;
        if ($logger instanceof LoggerInterface) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param array $settings
     * @param LoggerInterface $logger
     *
     * @return static
     */
    public static function build(array $settings = [], LoggerInterface $logger = null)
    {
        return new static($settings, $logger);
    }

    /**
     * Creates an adapter for given platform and client.
     * Wrap the client inside a platform adapter.
     *
     * @param ClientInterface $client
     *
     * @return AdapterInterface
     * @throws InputException
     */
    private function buildAdapter(ClientInterface $client)
    {
        // Build an adapter for client (as proxy between the connector and the client)
        $adapterFQCN = sprintf(self::ADAPTER_PATH, ucfirst($client->getProtocolName()));

        /** @var AdapterInterface $platFormAdapter */
        return new $adapterFQCN($client);
    }

    /**
     * Creates an connection for a given platform.
     *
     * @param string $protocolName type of connection (SOAP, REST etc)
     * @param string $platformName name of platform (webservices)
     *
     * @return ConnectorInterface
     * @throws InputException
     */
    public function create($protocolName, $platformName)
    {
        $config = $this->createPlatformConfig($platformName);

        // instantiate client factory for given protocol and pass along platform config.
        $factory = $this->createProtocolFactory($protocolName, $config);
        if ($this->getLogger() instanceof LoggerInterface) {
            $factory->setLogger($this->getLogger());
        }

        // build a protocol client (eg: Soap client, RPC client)
        $client = $factory->create($this->userSettings);

        // add the client to the wrapper (eg platform connector)
        return $this->buildConnector($client, $config);
    }

    /**
     * Build the connector with given client and platform config.
     *
     * @param ClientInterface         $client
     * @param PlatformConfigInterface $config
     *
     * @return ConnectorInterface
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    private function buildConnector(ClientInterface $client, PlatformConfigInterface $config)
    {
        $adapter = $this->buildAdapter($client);
        $connectorName = $config->getConnectorName();

        return new $connectorName($adapter);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Tries to find the factory for given protocol, to configure and create the ClientInterface.
     *
     * @param string                  $protocolName name of the protocol
     * @param PlatformConfigInterface $config       platform config object
     *
     * @return ClientFactoryInterface
     * @throws InputException
     */
    private function createProtocolFactory($protocolName, PlatformConfigInterface $config)
    {
        $clientFactory = sprintf(self::PROTOCOL_PATH, ucfirst($protocolName));
        if (!class_exists($clientFactory)) {
            throw new InputException("Could not find a factory for $protocolName");
        }

        /** @var ClientFactoryInterface $clientFactory */
        return $clientFactory::build($config);
    }

    /**
     * Build a platform config with given settings.
     *
     * @param string $platformName
     *
     * @return PlatformConfigInterface
     * @throws InputException
     */
    private function createPlatformConfig($platformName)
    {
        // create platform config from string
        $platformConfig = sprintf(self::PLATFORM_PATH, ucfirst($platformName));
        if (!class_exists($platformConfig)) {
            throw new InputException("Could not find a platformConfig for $platformName");
        }

        /** @var PlatformConfigInterface $platformConfig */
        $platformConfig = new $platformConfig();
        $platformConfig->loadFromArray($this->userSettings);

        return $platformConfig;
    }
}
