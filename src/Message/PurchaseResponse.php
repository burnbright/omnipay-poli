<?php

namespace Omnipay\Poli\Message;

use DOMDocument;
use Guzzle\Http\EntityBody;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use SimpleXMLElement;

/**
 * Poli Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * PurchaseResponse constructor.
     *
     * @param RequestInterface $request
     * @param EntityBody $data
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
    public function isSuccessful()
    {
        return isset($this->data['Success']) ? $this->data['Success'] : false;
    }

    /**
     * Do we need to redirect?
     *
     * @return bool
     */
    public function isRedirect()
    {
        return isset($this->data['NavigateURL']);
    }

    /**
     * Transaction reference
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->data['TransactionRefNo'];
    }

    /**
     * Error message, e.g.: '' = no error
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['ErrorMessage'] ?? $this->data['Message'] ?? null;
    }

    /**
     * Error code, e.g.: 0 = no error
     *
     * @return int
     */
    public function getCode()
    {
        return $this->data['ErrorCode'] ?? null;
    }

    /**
     * Redirection URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return $this->data['NavigateURL'];
        }
    }

    /**
     * Redirection method
     *
     * @return string
     */
    public function getRedirectMethod()
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
