<?php

namespace WebservicesNl\Soap\Client;

use WebservicesNl\Connector\Platform\PlatformConfigInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Client\ClientFactoryInterface;
use WebservicesNl\Common\Endpoint\Manager;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Soap\Config\ConfigFactory as SoapConfigFactory;
use WebservicesNl\Soap\Helper\GuzzleClientFactory;

/**
 * Class SoapFactory.
 * Create a SoapClient for a given platform (mainly webservices)
 */
class SoapFactory implements ClientFactoryInterface
{
    use LoggerAwareTrait;

    /**
     * @var SoapConfig
     */
    private $config;

    /**
     * SoapBuilder constructor.
     *
     * @param PlatformConfigInterface $platformConfig
     * @param LoggerInterface         $loggerInterface
     *
     * @throws InputException
     */
    public function __construct(PlatformConfigInterface $platformConfig, LoggerInterface $loggerInterface = null)
    {
        // convert a Platform config into a SoapConfig
        $this->config = SoapConfigFactory::config($platformConfig);
        $this->logger = $loggerInterface;
    }

    /**
     * @param PlatformConfigInterface $platformConfig
     * @param LoggerInterface|null    $loggerInterface
     *
     * @throws InputException
     * @return static
     */
    public static function build(PlatformConfigInterface $platformConfig, LoggerInterface $loggerInterface = null)
    {
        return new static($platformConfig, $loggerInterface);
    }

    /**
     * Build a soap client.
     *
     * @param array $settings
     *
     * @return SoapClient
     * @throws InputException
     */
    public function create(array $settings = [])
    {
        try {
            // create soap settings, with given settings and platform settings
            $soapSettings = SoapSettings::loadFromArray($settings);

            // create a manager for endpoint management
            $manager = $this->createEndpointManager($settings);

            // replace the native soap client transport with a curl client
            $curlClient = $this->createCurlClient($soapSettings->toArray(), $manager);

            // build a SoapClient (extends the native soap client)
            $soapClient = new SoapClient($soapSettings, $manager, $curlClient);
        } catch (\Exception $e) {
            throw new InputException('Could not create client. Reason: ' . $e->getMessage());
        }

        // set custom headers
        $soapHeaders = array_key_exists('soapHeaders', $settings) ?
            array_merge($settings['soapHeaders'], $this->config->getSoapHeaders()) : $this->config->getSoapHeaders();

        $soapClient->__setSoapHeaders($soapHeaders);
        if ($this->config->hasConverter()) {
            $soapClient->setConverter($this->config->getConverter());
        }

        if ($this->hasLogger() === true) {
            $soapClient->setLogger($this->logger);
            $this->logger->info(
                'Creating a SoapClient to connect to platform ' . $this->config->getPlatformConfig()->getPlatformName()
            );
            $this->logger->debug('Created SoapClient', ['SoapClient' => print_r($soapClient, true)]);
            $this->logger->debug('Settings', ['settings' => (array)$settings]);
        }

        return $soapClient;
    }

    /**
     * Returns if this SoapFactory is blessed with a loggerInterface.
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->logger instanceof LoggerInterface;
    }

    /**
     * Configure guzzle client for this soap factory.
     *
     * @param array   $settings
     * @param Manager $manager
     *
     * @return Client
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    private function createCurlClient(array $settings, Manager $manager)
    {
        $settings['url'] = (string)$manager->getActiveEndpoint()->getUri();

        return GuzzleClientFactory::build($this->config->getPlatformConfig(), $this->logger)->create($settings);
    }

    /**
     * @param array $settings
     *
     * @return Manager
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     */
    private function createEndpointManager(array $settings = [])
    {
        $endPoints = $this->getConfig()->getEndPoints();
        if (array_key_exists('endPoints', $settings)) {
            $endPoints = $settings['endPoints'] + $endPoints;
        }

        // create a manager
        $manager = new Manager();
        foreach ($endPoints as $endPoint) {
            $manager->createEndpoint($endPoint);
        }

        if ($this->hasLogger()) {
            $this->logger->info('Created endpoint manager', ['endpoint count' => $manager->getEndpoints()->count()]);
        }

        return $manager;
    }

    /**
     * @return SoapConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}
