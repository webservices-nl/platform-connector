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
 * Managing class (factory) for creating a PHP SoapClient for a given platform (mainly webservices)
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
     * Static function (LSB) for building this class.
     *
     * @param PlatformConfigInterface $platformConfig  PlatformConfig to create soapConfig Object
     * @param LoggerInterface|null    $loggerInterface optional logger interface
     *
     * @throws InputException
     * @return static
     */
    public static function build(PlatformConfigInterface $platformConfig, LoggerInterface $loggerInterface = null)
    {
        return new static($platformConfig, $loggerInterface);
    }

    /**
     * Creates a SoapClient with configured SoapConfig object and given additional settings.
     *
     * @param array $settings additional settings go here
     *
     * @return SoapClient
     * @throws InputException
     */
    public function create(array $settings = [])
    {
        // create soap settings, with given settings and platform settings
        $soapSettings = SoapSettings::loadFromArray($settings);

        // create a manager for endpoint management
        $manager = $this->createEndpointManager($settings);

        // replace the native soap client transport with a curl client

        $curlClient = $this->createCurlClient($soapSettings->toArray(), $manager);

        // build a SoapClient (extends the native soap client)
        $soapClient = new SoapClient($soapSettings, $manager, $curlClient);

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
     * Returns whether this instance is blessed with a LoggerInterface.
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->logger instanceof LoggerInterface;
    }

    /**
     * Configure PSR-7 guzzle client for this soap factory.
     *
     * @param array   $settings settings with extra guzzle settings
     * @param Manager $manager  endpoint Manager
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
     * Creates and configures a EndpointManager.
     *
     * If url key is present in settings array, this url will be set to the active endpoint
     *
     * @param array $settings optional settings
     *
     * @return Manager Endpoint manager
     * @throws InputException
     */
    private function createEndpointManager(array $settings = [])
    {
        // get the default end points from config object
        $endPoints = $this->getConfig()->getEndPoints();

        // merge defaults urls with custom url if present, custom url is set to active
        if (array_key_exists('url', $settings) && filter_var($settings['url'], FILTER_VALIDATE_URL) !== false) {
            array_unshift($endPoints, $settings['url']);
        }

        // Create EndPoint Manager
        $manager = new Manager();
        foreach ($endPoints as $endPoint) {
            $manager->createEndpoint($endPoint);
        }

        if ($this->hasLogger() === true) {
            $this->logger->info('Created endpoint manager', ['endpoint count' => $manager->getEndpoints()->count()]);
        }

        return $manager;
    }

    /**
     * Return SoapConfig.
     *
     * @return SoapConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}
