<?php
namespace Omnipay\Poli;

use Omnipay\GatewayTestCase;
use Omnipay\Common\CreditCard;

class PurchaseTest extends GatewayTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->gateway->setMerchantCode('ABCXYZ');
        $this->gateway->setAuthenticationCode('a;sldkfjasdf;aladjs');
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseRequestSuccess.txt');

        $response = $this->gateway->purchase(array(
            'amount' => '10.00',
            'currency' => 'NZD',
            'card' => $this->getValidCard(),
            'transactionID' => 12345,
            'returnUrl' => 'http://www.mytest.co.nz/shop/completepayment',
            'notifyUrl' => 'http://www.mytest.co.nz/shop/notifypayment',
            'cancelUrl' => 'http://www.mytest.co.nz/shop/cancelpayment'
        ))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('596464607245', $response->getTransactionReference());

        $this->assertEquals(
            "https://txn.apac.paywithpoli.com/?Token=ix3dphBp7oPMGCKSjH%2flf0OQSFipYvej",
            $response->getRedirectURL()
        );
    }

    public function testPurchaseFailure()
    {

        $this->setMockHttpResponse('PurchaseRequestFailure.txt');

        $response = $this->gateway->purchase(array(
            'amount' => '-12345.00',
            'currency' => 'NZD',
            'card' => $this->getValidCard(),
            'transactionID' => 12345,
            'returnUrl' => 'http://www.mytest.co.nz/shop/completepayment',
            'notifyUrl' => 'http://www.mytest.co.nz/shop/notifypayment',
            'cancelUrl' => 'http://www.mytest.co.nz/shop/cancelpayment'
        ))->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertEquals('14058', $response->getCode());
        $this->assertStringStartsWith(
            "Failed to initiate transaction for merchant",
            $response->getMessage()
        );
    }
}
