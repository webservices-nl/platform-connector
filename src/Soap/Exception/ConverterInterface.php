<?php

namespace WebservicesNl\Soap\Exception;

/**
 * Interface ConverterInterface
 */
interface ConverterInterface
{
    /**
     * Converts a protocol specific error into platform specific Domain Exception.
     *
     * @param mixed $error
     *
     * @return \Exception
     */
    public function convertToException($error);

    /**
     * Converts a Domain specific exception into protocol generic exception
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    public function convertFromException(\Exception $exception);
}