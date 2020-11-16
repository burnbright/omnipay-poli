<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Poli Complete Purchase Request
 *
 * @link http://www.polipaymentdeveloper.com/doku.php?id=gettransaction
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    protected $endpoint = '/api/v2/Transaction/GetTransaction';

    public function getData(): array
    {
        $this->validate(
            'merchantCode',
            'authenticationCode'
        );

        $token = $this->getToken();

        if (!$token) {
            $token = $this->httpRequest->query->get('token');
        }

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

    public function send(): RedirectResponseInterface
    {
        return $this->sendData($this->getData());
    }

    public function sendData($data): RedirectResponseInterface
    {
        $auth = base64_encode($this->getMerchantCode() . ':' . $this->getAuthenticationCode());
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'basic ' . $auth,
        ];

        // The Official way to add query params(4th argument of request())
        // does not seem to work, so appending to the endpoint uri as well.
        $url = $this->getEndpoint() . '?' . http_build_query($data);

        $httpResponse = $this->httpClient
            ->request('get', $url, $headers, http_build_query($data));

        return $this->response = new CompletePurchaseResponse($this, $httpResponse->getBody());
    }

    public function getToken(): ?string
    {
        return $this->getParameter('token');
    }
}
