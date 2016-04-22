<?php

namespace WebservicesNl\Connector\Client;

use Psr\Log\LoggerInterface;
use WebservicesNl\Platform\PlatformConfigInterface;

/**
 * Interface ClientFactoryInterface.
 *
 * Contract for protocol ClientFactories (used by the platform generator)
 */
interface ClientFactoryInterface
{
    /**
     * Initiate ClientFactory with given platform config.
     *
     * @param PlatformConfigInterface $platform
     *
     * @return static
     */
    public static function build(PlatformConfigInterface $platform);

    /**
     * Build ClientFactory providing connector.
     *
     * @param array $settings
     *
     * @return ClientInterface
     */
    public function create(array $settings = []);

    /**
     * Sets a logger (PSR-7).
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);
}
