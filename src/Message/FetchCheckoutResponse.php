<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Poli Checkout Response
 *
 */
class FetchCheckoutResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     *
     * @param RequestInterface $request
     * @param string $data
     * @throws InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = json_decode($data, true);
    }

    /**
     * Is the result a success?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return !$this->getCode();
    }

    /**
     * Do we need to redirect?
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return isset($this->data['NavigateURL']);
    }

    /**
     * Error message, e.g.: '' = no error
     *
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->data['ErrorMessage'] ?: null;
    }

    /**
     * Error code, e.g.: 0 or '' = no error
     *
     * @return int
     */
    public function getCode(): ?int
    {
        return $this->data['ErrorCode'] ?: null;
    }

    /**
     * Redirection URL
     *
     * @return string
     */
    public function getRedirectUrl(): ?string
    {
        if ($this->isRedirect()) {
            return $this->data['NavigateURL'];
        }

        return null;
    }

    /**
     * Redirection method
     *
     * @return string
     */
    public function getRedirectMethod(): string
    {
        return 'GET';
    }

    /**
     * Redirection data
     *
     * @return null
     */
    public function getRedirectData()
    {
        return null;
    }
}
