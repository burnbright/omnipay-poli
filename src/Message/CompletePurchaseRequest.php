<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use SimpleXMLElement;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Poli Complete Purchase Request
 * 
 * @link http://www.polipaymentdeveloper.com/doku.php?id=gettransaction
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    //protected $endpoint = "https://publicapi.apac.paywithpoli.com/api/Transaction/GetTransaction";
    protected $endpoint = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction';

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
        return $this->sendData($this->getData());
    }

    public function sendData($data)
    {
        $request = $this->httpClient->get($this->endpoint)
            ->setAuth($this->getMerchantCode(), $this->getAuthenticationCode());
        $request->getQuery()->replace($data);
        $httpResponse = $request->send();

        return $this->response = new CompletePurchaseResponse($this, $httpResponse->getBody());
    }
}
