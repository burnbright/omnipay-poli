<?php
namespace Omnipay\Poli;

use Omnipay\TestCase;
use Omnipay\Common\CreditCard;
use Omnipay\Poli\Message\PurchaseRequest;

class PurchaseRequestTest extends TestCase
{

	public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * check that MerchantRef outputs correctly, based on supplied params
     */
	public function testMerchantRef()
	{

		$params = array(
            'amount' => '12.00',
            'description' => 'Test Product',
            'currency' => 'NZD',
            'merchantCode' => 'ASDF',
            'authenticationCode' => '1234',
            'transactionId' => 123,
            'returnUrl' => 'https://www.example.com/return',
            'cancelUrl' => 'https://www.example.com/cancel',
        );
		$this->request->initialize($params);

		$this->assertEquals(123, $this->request->getCombinedMerchantRef());
		$data = $this->request->getData();
		$this->assertEquals(123, $data['MerchantRef']);
		$this->assertEquals(1, $data['MerchantReferenceFormat']);

		$params['card'] = $this->getValidCard();
		$this->request->initialize($params);
		$this->assertEquals("Example User||123", $this->request->getCombinedMerchantRef());

	}

}
