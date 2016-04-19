<?php

namespace WebservicesNl\Test\Soap\Config\Platform\Webservices;

use WebservicesNl\Soap\Config\Platform\Webservices\Config as WebservicesConfig;

/**
 * Class WebservicesConfigTest.
 *
 */
class WebservicesConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testConfigCreationWithInvalidArgument()
    {
        WebservicesConfig::configure(null);
    }
}
