<?php
namespace Omnipay\Poli;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());

        $this->gateway->setMerchantCode('ABCXYZ');
        $this->gateway->setAuthenticationCode('a;sldkfjasdf;aladjs');
    }

    public function testproductionMode()
    {
        $this->assertEquals(
            "https://poliapi.apac.paywithpoli.com/api/v2/Transaction/Initiate",
            $this->gateway->purchase()->getEndpoint()
        );

        $this->assertEquals(
            "https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction",
            $this->gateway->fetchCheckout()->getEndpoint()
        );

        $this->assertEquals(
            "https://poliapi.apac.paywithpoli.com/api/v2/Transaction/GetTransaction",
            $this->gateway->completePurchase()->getEndpoint()
        );
    }

    public function testTestMode()
    {
        $this->gateway->setTestMode(true);

        $this->assertEquals(
            "https://poliapi.uat1.paywithpoli.com/api/v2/Transaction/Initiate",
            $this->gateway->purchase()->getEndpoint()
        );

        $this->assertEquals(
            "https://poliapi.uat1.paywithpoli.com/api/v2/Transaction/GetTransaction",
            $this->gateway->fetchCheckout()->getEndpoint()
        );

        $this->assertEquals(
            "https://poliapi.uat1.paywithpoli.com/api/v2/Transaction/GetTransaction",
            $this->gateway->completePurchase()->getEndpoint()
        );
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

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertEquals('596464607245', $response->getTransactionReference());

        $this->assertEquals(
            "https://txn.apac.paywithpoli.com/?token=ix3dphBp7oPMGCKSjH%2flf0OQSFipYvej",
            $response->getRedirectURL()
        );
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseRequestFailure.txt');

        try {
            $response = $this->gateway->purchase(array(
                'amount' => '-12345.00',
                'currency' => 'NZD',
                'card' => $this->getValidCard(),
                'transactionID' => 12345,
                'returnUrl' => 'http://www.mytest.co.nz/shop/completepayment',
                'notifyUrl' => 'http://www.mytest.co.nz/shop/notifypayment',
                'cancelUrl' => 'http://www.mytest.co.nz/shop/cancelpayment'
            ))->send();
            $this->assertFalse(true, 'Expecting an InvalidRequestException');
        } catch (InvalidRequestException $ire) {
            $this->assertEquals('A negative amount is not allowed.', $ire->getMessage());
        }
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertEquals('1025', $response->getCode());
//        $this->assertEquals('The CurrencyAmount field is out of range.', $response->getMessage());
    }

    public function testCompletePurchaseSuccess()
    {
        //set mock GET data
        $this->getHttpRequest()->query->replace(array(
            'token' => '5lvIbQXpE7Og7yZcir2HJgYxvSsfarVc'
        ));
        $this->setMockHttpResponse('CompletePurchaseRequestSuccess.txt');
        $response = $this->gateway->completePurchase()->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertEquals("396414606841", $response->getTransactionReference());
    }

    public function testNotifyCompletePurchaseSuccess()
    {
        //set mock POST data
        $this->getHttpRequest()->request->replace(array(
            'Token' => '5lvIbQXpE7Og7yZcir2HJgYxvSsfarVc'
        ));
        $this->setMockHttpResponse('CompletePurchaseRequestSuccess.txt');
        $response = $this->gateway->completePurchase()->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertEquals("396414606841", $response->getTransactionReference());
    }
}
