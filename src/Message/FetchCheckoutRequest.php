<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RedirectResponseInterface;

class FetchCheckoutRequest extends AbstractRequest
{
    protected $endpoint = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction';

    public function getMerchantCode(): ?string
    {
        return $this->getParameter('merchantCode');
    }

    public function setMerchantCode($value): self
    {
        return $this->setParameter('merchantCode', $value);
    }

    public function getAuthenticationCode(): ?string
    {
        return $this->getParameter('authenticationCode');
    }

    public function setAuthenticationCode($value): self
    {
        return $this->setParameter('authenticationCode', $value);
    }

    public function getData(): array
    {
        $this->validate(
            'merchantCode',
            'authenticationCode'
        );
        $data = array();
        return $data;
    }

    public function send(): RedirectResponseInterface
    {
        $data = $this->getData();
        return $this->sendData($data);
    }

    public function sendData($data): RedirectResponseInterface
    {
        $token = $this->httpRequest->query->get('token');
        $url = $this->endpoint . '?token=' . urlencode($token);

        $merchantCode = $this->getMerchantCode();
        $authenticationCode = $this->getAuthenticationCode();
        $auth = base64_encode($merchantCode . ":" . $authenticationCode); //'S61xxxxx:AuthCode123');

        $httpResponse = $this->httpClient->request(
            'get',
            $url,
            array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $auth,
            )
        );

        return $this->response = new FetchCheckoutResponse($this, $httpResponse->getBody(true));
    }
}
