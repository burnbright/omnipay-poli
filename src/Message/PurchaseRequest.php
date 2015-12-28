<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Poli Purchase Request
 * 
 * @link http://www.polipaymentdeveloper.com/doku.php?id=initiate
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
        $data['CancellationURL'] = $this->getCancelUrl();
        $data['MerchantData'] = $this->getTransactionId();
        $data['MerchantDateTime'] = date('Y-m-d\TH:i:s');
        $data['MerchantHomePageURL'] = $this->getCancelUrl();
        $data['MerchantReference'] = $this->getCombinedMerchantRef();
        $data['MerchantReferenceFormat'] = 1;
        $data['NotificationURL'] = $this->getNotifyUrl();
        $data['SuccessURL'] = $this->getReturnUrl();
        $data['Timeout'] = 0;
        $data['FailureURL'] = $this->getReturnUrl();
        $data['UserIPAddress'] = $this->getClientIp();

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
        return $this->sendData($this->getData());
    }

    public function sendData($data)
    {
        $merchantCode = $this->getMerchantCode();
        $authenticationCode = $this->getAuthenticationCode();
        $auth = base64_encode($merchantCode.":".$authenticationCode); //'S61xxxxx:AuthCode123');
        unset($data['MerchantCode'], $data['AuthenticationCode']);

        //$postdata = $this->packageData($data);
        $postdata = json_encode($data);
        $httpRequest = $this->httpClient->post(
            $this->endpoint,
            array(
                'Content-Type'=>'application/json',
                'Authorization' => 'Basic '.$auth,
            ),
            $postdata
        );
        $httpResponse = $httpRequest->send();
        return $this->response = new PurchaseResponse($this, $httpResponse->getBody());
    }

    protected function packageData($data)
    {
        $authenticationcode = $data['AuthenticationCode'];
        unset($data['AuthenticationCode']);
        $fields = "";
        foreach ($data as $field => $value) {
            $fields .= str_repeat(" ", 24)."<dco:$field>$value</dco:$field>\n";
        }
        $namespace = "http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts";
        $i_namespace = "http://www.w3.org/2001/XMLSchema-instance";
        $dco_namespace = "http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO";

        return '<?xml version="1.0" encoding="utf-8" ?>
                <InitiateTransactionRequest xmlns="'.$namespace.'" xmlns:i="'.$i_namespace.'">
                    <AuthenticationCode>'. $authenticationcode.'</AuthenticationCode>
                    <Transaction xmlns:dco="'.$dco_namespace.'">'
                        .$fields.
                    '</Transaction>
                </InitiateTransactionRequest>';
    }
}
