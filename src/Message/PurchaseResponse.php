<?php

namespace Omnipay\Poli\Message;

use DOMDocument;
use SimpleXMLElement;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Poli Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    protected $error;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $xml = new SimpleXMLElement($data);
        if (!$xml->TransactionStatusCode) {
            throw new InvalidResponseException;
        }
        $errors = $xml->Errors->children(
            'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO'
        );
        $this->error = $errors[0]; //only store first error
        $this->data = $xml->Transaction->children(
            'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO'
        );
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return isset($this->data->NavigateURL);
    }

    public function getTransactionReference()
    {
        return isset($this->data->TransactionRefNo) ? (string)$this->data->TransactionRefNo : null;
    }

    public function getMessage()
    {
        return isset($this->error->Message) ? (string)$this->error->Message : null;
    }

    public function getCode()
    {
        return isset($this->error->Code) ? (string)$this->error->Code : null;
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return (string)$this->data->NavigateURL;
        }
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }
}
