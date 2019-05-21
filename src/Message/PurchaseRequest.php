<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Poli Purchase Request
 *
 * @link https://www.polipayments.com/InitiateTransaction
 */
class PurchaseRequest extends AbstractRequest
{

    protected $endpoint = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/Initiate';

    public function getMerchantCode()
    {
        return $this->getParameter('merchantCode');
    }

    public function setMerchantCode($value)
    {
        return $this->setParameter('merchantCode', $value);
    }

    public function getAuthenticationCode()
    {
        return $this->getParameter('authenticationCode');
    }

    public function setAuthenticationCode($value)
    {
        return $this->setParameter('authenticationCode', $value);
    }

    public function getData()
    {
        $this->validate(
            'merchantCode',
            'authenticationCode',
            'transactionId',
            'currency',
            'amount',
            'returnUrl',
            'cancelUrl'
        );

        $data = array();
        $data['Amount'] = $this->getAmount();
        $data['CurrencyCode'] = $this->getCurrency();
        $data['MerchantReference'] = $this->getCombinedMerchantRef();
        $data['MerchantReferenceFormat'] = 1;
        $data['MerchantData'] = $this->getTransactionId();
        $data['MerchantHomepageURL'] = $this->getCancelUrl();
        $data['SuccessURL'] = $this->getReturnUrl();
        $data['FailureURL'] = $this->getReturnUrl();
        $data['CancellationURL'] = $this->getCancelUrl();
        $data['NotificationURL'] = $this->getNotifyUrl();
        $data['Timeout'] = 0;

        return $data;
    }

    /**
     * Generate reference data
     * @link http://www.polipaymentdeveloper.com/doku.php?id=nzreconciliation
     */
    public function getCombinedMerchantRef()
    {
        $card = $this->getCard();
        $id = $this->cleanField($this->getTransactionId());
        if ($card && $card->getName()) {
            $data = array($this->cleanField($card->getName()), "", $id);
            return implode("|", $data);
        }

        return $id;
    }

    /**
     * Data in reference field must not contain illegal characters
     */
    protected function cleanField($field)
    {
        return substr($field, 0, 12);
    }

    public function send()
    {
        
        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        $postdata = json_encode($this->getData());
        $httpRequest = $this->httpClient->post(
            $this->endpoint,
            array('Content-Type'=>'application/json'),
            $postdata
        )->setAuth($this->getMerchantCode(), $this->getAuthenticationCode());
        $httpResponse = $httpRequest->send();
        return $this->response = new PurchaseResponse($this, $httpResponse->getBody());
    }
}
