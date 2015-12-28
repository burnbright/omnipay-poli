<?php

namespace Omnipay\Poli\Message;

use Guzzle\Http\EntityBodyInterface;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Poli Checkout Response
 *
 * Example of json data returned:

{
"CountryName": "New Zealand",
"FinancialInstitutionCountryCode": "iBankNZ01",
"TransactionID": "073d33e4-93a2-4048-ac49-be50cfdaf2c1",
"MerchantEstablishedDateTime": "2015-12-28T10:50:19.753",
"PayerAccountNumber": "98742364",
"PayerAccountSortCode": "123456",
"MerchantAccountSortCode": "389006",
"MerchantAccountName": "Blue Horn Ltd",
"MerchantData": "\"{\\\"user_id\\\":\\\"37\\\"}\"",
"CurrencyName": "New Zealand Dollar",
"TransactionStatus": "Completed",
"IsExpired": false,
"MerchantEntityID": "e38a60ae-e39b-477e-976e-448c4cb8c0d0",
"UserIPAddress": "115.178.250.107",
"POLiVersionCode": "4 ",
"MerchantName": "GiveMeQuotes",
"TransactionRefNo": "996423005190",
"CurrencyCode": "NZD",
"CountryCode": "NZ",
"PaymentAmount": 25.00,
"AmountPaid": 25.00,
"EstablishedDateTime": "2015-12-28T10:50:19.767",
"StartDateTime": "2015-12-28T10:50:19.767",
"EndDateTime": "2015-12-28T10:54:18.83",
"BankReceipt": "39713232-84969",
"BankReceiptDateTime": "28 December 2015 10:54:18",
"TransactionStatusCode": "Completed",
"ErrorCode": "",
"ErrorMessage": "",
"FinancialInstitutionCode": "iBankNZ01",
"FinancialInstitutionName": "iBank NZ 01",
"MerchantReference": "GiveMeQuotes25Credit",
"MerchantAccountSuffix": "000",
"MerchantAccountNumber": "0726073",
"PayerFirstName": "Mr",
"PayerFamilyName": "DemoShopper",
"PayerAccountSuffix": ""
}

 */
class FetchCheckoutResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     *
     * @param RequestInterface $request
     * @param string $data
     * @throws InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = json_decode($data, true);
    }

    /**
     * Is the result a success?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return !$this->getCode();
    }

    /**
     * Do we need to redirect?
     *
     * @return bool
     */
    public function isRedirect()
    {
        return isset($this->data['NavigateURL']);
    }

    /**
     * Error message, e.g.: '' = no error
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['ErrorMessage'];
    }

    /**
     * Error code, e.g.: 0 or '' = no error
     *
     * @return int
     */
    public function getCode()
    {
        return $this->data['ErrorCode'];
    }

    /**
     * Redirection URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return $this->data['NavigateURL'];
        }
    }

    /**
     * Redirection method
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * Redirection data
     *
     * @return null
     */
    public function getRedirectData()
    {
        return null;
    }
}
