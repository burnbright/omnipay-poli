<?php
namespace Omnipay\Poli;

use Omnipay\GatewayTestCase;
use Omnipay\Common\CreditCard;

class CompletePurchaseTest extends GatewayTestCase
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

    /**
     * @expectedException Omnipay\Common\Exception\InvalidRequestException
     */
    public function testCompletePurchaseInvalidRequest()
    {
        $response = $this->gateway->completePurchase()->send();
    }

    public function testCompletePurchaseFailure()
    {
         //set mock GET data
         $this->getHttpRequest()->query->replace(array(
            'token' => '5lvIbQXpE7Og7yZcir2HJgYxvSsfarVcfoo'
         ));
        $this->setMockHttpResponse('CompletePurchaseRequestFailure.txt');
        $response = $this->gateway->completePurchase()->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('14052', $response->getCode());
        $this->assertStringStartsWith('The transaction token provided is corrupted', $response->getMessage());
    }
}
