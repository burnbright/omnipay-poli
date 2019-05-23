<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Poli Complete Purchase Request
 *
 * @link https://www.polipayments.com/GetTransaction
 */
class CompletePurchaseRequest extends PurchaseRequest
{

    protected $endpoint = "https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction";

    public function getData()
    {
        $this->validate(
            'merchantCode',
            'authenticationCode'
        );

        $token = $this->httpRequest->query->get('token');
        if (!$token) {
            //this may be a POST nudge request, so look for the token there
            $token = $this->httpRequest->request->get('Token');
        }

        if (!$token) {
            throw new InvalidRequestException('Transaction token is missing');
        }

        $data = array();
        $data['token'] = $token;
        
        return $data;
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

        $request = $this->httpClient->get($this->endpoint)
            ->setAuth($this->getMerchantCode(), $this->getAuthenticationCode());
        $request->getQuery()->replace($this->getData());
        $httpResponse = $request->send();
        return $this->response = new CompletePurchaseResponse($this, $httpResponse->getBody());
    }
}
