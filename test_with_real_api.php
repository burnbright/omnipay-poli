<?php

/**
 * 1. Run this as a command line, e.g.: `php test_with_real_api.php`
 * 2. Enter the merchant code and authentication code
 * 3. It should print out the response print_f and 'SUCCESSFUL' or 'FAILED' at the bottom
 */

require_once 'vendor/autoload.php';

use Omnipay\Omnipay;

$merchantCode = readline("Enter the merchant code: ");
$authenticationCode = readline("Enter the authentication code: ");

$gateway = Omnipay::create('Poli');
/** @var $gateway \Omnipay\Poli\Gateway */
$gateway->setMerchantCode($merchantCode);
$gateway->setAuthenticationCode($authenticationCode);

$response = $gateway->purchase(array(
    'merchantCode' => $gateway->getMerchantCode(),
    'authenticationCode' => $gateway->getAuthenticationCode(),
    'transactionId' => 'TEST'.rand(1000,9999),
    'currency' => 'NZD',
    'amount' => '1.00',
    'returnUrl' => 'https://localhost/return',
    'cancelUrl' => 'https://localhost/cancel'
))->send();

print_r($response);
echo "\n";

if ($response->isSuccessful()) {
    echo "SUCCESSFUL";
} else {
    echo "FAILED";
}

echo "\n";



