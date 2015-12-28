<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest;

class FetchCheckoutRequest extends AbstractRequest
{
    protected $endpoint = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction';

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
            'authenticationCode'
        );
        $data = array();
        return $data;
    }

    public function send()
    {
        $data = $this->getData();
        return $this->sendData($data);
    }

    public function sendData($data)
    {
        $token = $this->httpRequest->query->get('token');
        $url = $this->endpoint.'?token='.urlencode($token);

        $merchantCode = $this->getMerchantCode();
        $authenticationCode = $this->getAuthenticationCode();
        $auth = base64_encode($merchantCode.":".$authenticationCode); //'S61xxxxx:AuthCode123');

        $httpRequest = $this->httpClient->get(
            $url,
            array(
                'Content-Type'=>'application/json',
                'Authorization' => 'Basic '.$auth,
            )
        );
        $httpResponse = $httpRequest->send();
        return $this->response = new FetchCheckoutResponse($this, $httpResponse->getBody(true));

    }
}
