<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Poli Purchase Request
 *
 * @link http://www.polipaymentdeveloper.com/doku.php?id=initiate
 */
class PurchaseRequest extends AbstractRequest
{
    protected $endpoint = 'https://poliapi.apac.paywithpoli.com/api/v2/Transaction/Initiate';

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

    public function getTimeout(): int
    {
        return $this->getParameter('timeout') ?: 900;
    }

    public function setTimeout(int $value): self
    {
        return $this->setParameter('timeout', abs($value));
    }

    public function getMerchantData()
    {
        return $this->getParameter('merchantData');
    }

    public function setMerchantData($value): self
    {
        return $this->setParameter('merchantData', $value);
    }

    public function getMerchantReference(): ?string
    {
        return $this->getParameter('merchantReference') ?: $this->getCombinedMerchantRef();
    }

    public function setFailureURL($value): self
    {
        return $this->setParameter('failureURL', $value);
    }

    public function getFailureURL(): ?string
    {
        return $this->getParameter('failureURL');
    }

    public function setMerchantReference($value): self
    {
        return $this->setParameter('merchantReference', preg_replace([
            '/[\#@\-_=:?\.\/]/',
            '/\s\s/'
        ], ' ', $value));
    }

    public function getData(): array
    {
        $this->validate(
            'merchantCode',
            'authenticationCode',
            'currency',
            'amount',
            'returnUrl',
            'cancelUrl'
        );

        $data = array();
        $data['Amount'] = $this->getAmount();
        $data['CurrencyCode'] = $this->getCurrency();
        $data['CancellationURL'] = $this->getCancelUrl();
        $data['MerchantData'] = json_encode($this->getMerchantData());
        $data['MerchantDateTime'] = date('Y-m-d\TH:i:s');
        $data['MerchantHomePageURL'] = $this->getCancelUrl();
        $data['MerchantReference'] = $this->getMerchantReference();
        $data['MerchantReferenceFormat'] = 1;
        $data['NotificationURL'] = $this->getNotifyUrl();
        $data['SuccessURL'] = $this->getReturnUrl();
        $data['Timeout'] = $this->getTimeout();
        $data['FailureURL'] = $this->getFailureURL() ?: $this->getReturnUrl();
        $data['UserIPAddress'] = $this->getClientIp();

        return $data;
    }

    /**
     * Generate reference data
     * @link http://www.polipaymentdeveloper.com/doku.php?id=nzreconciliation
     */
    public function getCombinedMerchantRef(): string
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
    protected function cleanField(string $field): string
    {
        return substr($field, 0, 12);
    }

    public function send(): RedirectResponseInterface
    {
        return $this->sendData($this->getData());
    }

    public function sendData($data): RedirectResponseInterface
    {
        $merchantCode = $this->getMerchantCode();
        $authenticationCode = $this->getAuthenticationCode();
        $auth = base64_encode($merchantCode . ":" . $authenticationCode); //'S61xxxxx:AuthCode123');
        unset($data['MerchantCode'], $data['AuthenticationCode']);

        $postdata = json_encode($data);
        $httpResponse = $this->httpClient->request(
            'post',
            $this->endpoint,
            array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $auth,
            ),
            $postdata
        );

        return $this->response = new PurchaseResponse($this, $httpResponse->getBody());
    }
}
