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

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = json_decode($data);
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
        return isset($this->data->ErrorMessage) ? (string)$this->data->ErrorMessage : null;
    }

    public function getCode()
    {
        return isset($this->data->ErrorCode) ? (string)$this->data->ErrorCode : null;
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
