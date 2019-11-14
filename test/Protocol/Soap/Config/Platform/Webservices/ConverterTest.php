<?php

namespace WebservicesNl\Test\Protocol\Soap\Client\Config\Platform\Webservices;

use WebservicesNl\Common\Exception\Exception;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter;

/**
 * Class ConverterTest
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFaults
     *
     * @param string $errorCode
     */
    public function testConverter($errorCode)
    {
        $converter = Converter::build();
        $detail = new \stdClass();
        $detail->errorcode = $errorCode;
        $soapFault = new \SoapFault('Server', 'some detailed error message', null, $detail);
        $errorClassFQ = 'WebservicesNl\Common\Exception\\' . str_replace('.', '\\', $errorCode) . 'Exception';

        try {
            $exception = $converter->convertToException($soapFault);
            static::assertInstanceOf($errorClassFQ, $exception);
        } catch (Exception $e) {
            static::assertInstanceOf($errorClassFQ, $e);
        }
    }

    /**
     * @throws \WebservicesNl\Common\Exception\ServerException
     */
    public function testConverterBad()
    {
        $this->expectException(\WebservicesNl\Common\Exception\ServerException::class);
        $this->expectExceptionMessage('Could not convert errorCode: \'FakeClass\'');

        $converter = Converter::build();
        $detail = new \stdClass();
        $detail->errorcode = 'FakeClass';
        $soapFault = new \SoapFault('Server', 'some detailed error message', null, $detail);
        $converter->convertToException($soapFault);
    }

    public function testConvertFromException()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not yet implemented');

        $converter = Converter::build();
        $converter->convertFromException(new Exception());
    }

    /**
     * @return array
     */
    public function getFaults()
    {
        return [
            ['Client.Authentication'],
            ['Client.Authentication.HostRestriction'],
            ['Client.Authentication.Password'],
            ['Client.Authentication.Username'],
            ['Client.Authorization'],
            ['Client.Input'],
            ['Client.Input.FormatIncorrect'],
            ['Client.Input.Incomplete'],
            ['Client.Input.Invalid'],
            ['Client.Payment'],
            ['Server'],
            ['Server.Data'],
            ['Server.Data.NotFound'],
            ['Server.Data.PageNotFound'],
            ['Server.Unavailable'],
            ['Server.Unavailable.InternalError'],
            ['Server.Unavailable.Temporary'],
        ];
    }
}
