<?php

namespace Webservicesnl\test\Soap\Client\Config;

use Webservicesnl\Soap\Client\Config\WebservicesConfig;

/**
 * Class WebservicesConfigTest.
 *
 */
class WebservicesConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Settings is not a SoapSettings object or array
     *
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testConfigCreationWithBadArgument()
    {
        WebservicesConfig::configure(null);
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testConfigCreationWithEmptyArray()
    {
        WebservicesConfig::configure([]);
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \Webservicesnl\Exception\Client\InputException
     */
    public function testConfigCreationWithValidArray()
    {
        $result = WebservicesConfig::configure(['username' => 'johndoe', 'password' => 'topsecret']);

        $this->assert('username', $result);
    }
}
