<?php

namespace WebservicesNl\Soap\Exception;

interface ConverterInterface
{
    /**
     * Converts a soapFault back into platform specific Domain Exception.
     *
     * @param \SoapFault $fault
     *
     * @return \Exception
     */
    public function convertToException(\SoapFault $fault);
}
