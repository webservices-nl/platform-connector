<?php

namespace WebservicesNl\Soap\Exception;

/**
 * Abstract Class SoapFault.
 *
 * Extends SoapFault into a Webservices SoapFault (just for getters and setters)
 *
 * @link http://php.net/manual/en/soapfault.soapfault.php
 */
class SoapFault extends \SoapFault
{
    /**
     * More details about the cause of the error.
     *
     * Required if the fault is an error related to the SOAPBody object. If, for example, the fault code is Client,
     * indicating that the message could not be processed because of a problem in the SOAPBody object,
     * the SOAPFault object must contain a Detail object that gives details about the problem.
     * If a SOAPFault object does not contain a Detail object, it can be assumed that the SOAPBody object was processed
     * successfully.
     *
     * @var string
     */
    public $detail;

    /**
     * A string identifying the actor that caused the error.
     *
     * Required if the SOAPHeader object contains one or more actor attributes; optional if no actors are specified.
     * Meaning that the only actor is the ultimate destination. The fault actor, which is specified as a URI, identifies
     * who caused the fault.
     *
     * @var string
     */
    public $faultActor;

    /**
     * The Soap Fault code (either Sender or Receiver).
     *
     * @link https://www.w3.org/TR/soap12-part1/#faultcodes
     *
     * @var string
     */
    public $faultCode;

    /**
     * Can be used to select the proper fault encoding from WSDL.
     *
     * @var string
     */
    public $faultName;

    /**
     * The error message of the SoapFault.
     * Always required. A human-readable explanation of the fault.
     *
     * @var string
     */
    public $faultString = 'Something went wrong';

    /**
     * Can be used during SOAP header handling to report an error in the response header.
     *
     * @var string
     */
    public $headerFault;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * SoapFault constructor.
     *
     * @param string $message
     * @param int    $detail
     *
     * @internal param string $faultstring
     */
    public function __construct($message, $detail)
    {
        $this->message = $message;

        parent::SoapFault($this->getFaultCode(), $this->getFaultString(), $this->faultActor, $detail);
    }

    /**
     * @return string
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }

    /**
     * @return string
     */
    public function getFaultActor()
    {
        return $this->faultActor;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @return string
     */
    public function getFaultName()
    {
        return $this->faultName;
    }

    /**
     * @return string
     */
    public function getHeaderFault()
    {
        return $this->headerFault;
    }

    /**
     * @return string
     */
    public function getFaultString()
    {
        return $this->faultString;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
