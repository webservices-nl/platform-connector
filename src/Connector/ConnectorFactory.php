<?php

namespace WebservicesNl\Connector;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Client\ClientFactoryInterface;
use WebservicesNl\Common\Client\ClientInterface;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Connector\Adapter\AdapterInterface;
use WebservicesNl\Connector\Platform\PlatformConfigInterface;

/**
 * ConnectorFactory for creating platform connector.
 *
 * Helps with creating a connector for a given platform over a certain protocol.
 * Provide some user settings and afterwards create platforms like a boss.
 */
class ConnectorFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     */
    public function __construct(array $settings = [])
    {
        $this->userSettings = $settings;
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
     * Creates a Adapter for given platform and client.
     * Wrap the client inside a platform adapter
     *
     * @param ClientInterface $client
     *
     * @return mixed
     * @throws InputException
     */
    private function buildAdapter(ClientInterface $client)
    {
        // Build an adapter for client (as proxy between the connector and the client)
        $adapterFQCN = sprintf(__NAMESPACE__ . '\\Adapter\\' . ucfirst($client->getProtocolName()) . 'Adapter');

        /** @var AdapterInterface $platFormAdapter */
        return new $adapterFQCN($client);
    }

    /**
     * Creates an connection for a given platform.
     *
     * @param string $protocolName type of connection (SOAP, REST etc)
     * @param string $platformName
     *
     * @return ConnectorInterface
     * @throws InputException
     */
    public function create($protocolName, $platformName)
    {
        $config = $this->createPlatformConfig($platformName);

        // instantiate client factory for given protocol and pass along platform config. Ask Factory build a client
        $client = $this->createClientFactory($protocolName, $config)->create($this->userSettings);

        // wrap the client in a connector
        return $this->buildConnector($client, $config);
    }

    /**
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
     * Tries to find the factory for given protocol.
     *
     * @param string                  $protocolName
     * @param PlatformConfigInterface $config
     *
     * @return ClientFactoryInterface
     * @throws InputException
     */
    private function createClientFactory($protocolName, PlatformConfigInterface $config)
    {
        $protocolName = ucfirst($protocolName);
        $clientFactory = sprintf('WebservicesNl\\%1$s\\Client\\%1$sFactory', $protocolName);
        if (!class_exists($clientFactory)) {
            throw new InputException("Could not find a factory for $protocolName");
        }

        /** @var ClientFactoryInterface $clientFactory */
        return $clientFactory::build($config, $this->getLogger());
    }

    /**
     * Build a platform config with given settings
     *
     * @param string $platformName
     *
     * @return PlatformConfigInterface
     * @throws InputException
     */
    private function createPlatformConfig($platformName)
    {
        // create platform config from string
        $platformName = ucfirst($platformName);
        $platformConfig = implode("\\", [__NAMESPACE__, 'Platform', $platformName, 'Config']);
        if (!class_exists($platformConfig)) {
            throw new InputException("Could not find a platformConfig for $platformName");
        }

        /** @var PlatformConfigInterface $platformConfig */
        $platformConfig = new $platformConfig();
        $platformConfig->loadFromArray($this->userSettings);

        return $platformConfig;
    }
}
