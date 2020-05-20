<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Poli Complete Purchase Response
 */
class CompletePurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    public function __construct(RequestInterface $request, $data)
    {
        $data = json_decode($data);
        if (!$data || !$data->TransactionRefNo) {
            throw new InvalidResponseException;
        }
        $this->data = $data;
    }

    public function isSuccessful(): bool
    {
        return !$this->getCode() && $this->data->TransactionStatusCode === "Completed";
    }

    public function getTransactionReference(): ?string
    {
        if ($this->data->TransactionRefNo) {
            return $this->data->TransactionRefNo;
        }

        return null;
    }

    public function getCode(): ?int
    {
        if ($this->data->ErrorCode) {
            return $this->data->ErrorCode;
        }

        return null;
    }

    public function getMessage(): ?string
    {
        if ($this->data->ErrorMessage) {
            return $this->data->ErrorMessage;
        }

        return null;
    }
}
