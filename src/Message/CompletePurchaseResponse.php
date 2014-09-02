<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Poli Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse
{

    public function __construct(RequestInterface $request, $data)
    {
        $data = json_decode($data);
        if (!$data || !$data->TransactionRefNo) {
            throw new InvalidResponseException;
        }
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return !$this->getCode() && $this->data->TransactionStatusCode === "Completed";
    }

    public function getTransactionReference()
    {
        if ($this->data->TransactionRefNo) {
            return $this->data->TransactionRefNo;
        }
    }

    public function getCode()
    {
        if ($this->data->ErrorCode) {
            return $this->data->ErrorCode;
        }
    }

    public function getMessage()
    {
        if ($this->data->ErrorMessage) {
            return $this->data->ErrorMessage;
        }
    }
}
