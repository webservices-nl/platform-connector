<?php

namespace WebservicesNl\Platform;

/**
 * Interface PlatformConfigInterface.
 *
 * All platform specific settings go here.
 */
interface PlatformConfigInterface
{
    /**
     * Return concrete class name.
     * Optional return as Fully Qualified Class Name.
     *
     * @param bool $FQCN
     *
     * @return string
     */
    public function getClassName($FQCN = false);

    /**
     * Return this name of the connector ConnectorInterface.
     *
     * @return string
     */
    public function getConnectorName();

    /**
     * return Platform name.
     *
     * @return string
     */
    public function getPlatformName();

    /**
     * Returns minimal configured class object.
     *
     * @param array $settings
     *
     * @return mixed
     */
    public function loadFromArray(array $settings = []);

    /**
     * Return object as an array.
     *
     * @return array
     */
    public function toArray();
}
