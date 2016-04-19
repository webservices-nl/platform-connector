<?php

namespace WebservicesNl\Soap\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use WebservicesNl\Common\Client\ClientFactoryInterface;
use WebservicesNl\Connector\Platform\PlatformConfigInterface;

/**
 * Class HttpClientFactory.
 */
class GuzzleClientFactory implements ClientFactoryInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $platform;

    /**
     * HttpClientFactory constructor.
     *
     * @param PlatformConfigInterface $platform
     * @param LoggerInterface|null    $logger
     */
    public function __construct(PlatformConfigInterface $platform, LoggerInterface $logger = null)
    {
        $this->platform = $platform;
        $this->logger = $logger;
    }

    /**
     * @param PlatformConfigInterface $platform
     * @param LoggerInterface         $logger
     *
     * @return GuzzleClientFactory
     */
    public static function build(PlatformConfigInterface $platform, LoggerInterface $logger = null)
    {
        return new static($platform, $logger);
    }

    /**
     * @param array $settings
     *
     * @return Client
     */
    public function create(array $settings = [])
    {
        $stack = null;
        $settings = $this->platform->toArray() + $settings;

        if ($this->getLogger() instanceof LoggerInterface) {
            $stack = HandlerStack::create();
            $stack->push(Middleware::log($this->getLogger(), new MessageFormatter('{request} - {response}')));
        }

        return new Client(
            [
                'base_url'           => (string)$settings['url'],
                'handler'            => $stack,
                'exceptions'         => (bool)$settings['exceptions'],
                'timeout'            => (float)$settings['responseTimeout'],
                'connection_timeout' => (float)$settings['connectionTimeout'],
            ]
        );
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
