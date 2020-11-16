<?php

namespace Omnipay\Poli\Message;

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;

/**
 * Poli Purchase Request
 *
 * @link http://www.polipaymentdeveloper.com/doku.php?id=initiate
 */
abstract class AbstractRequest extends OmnipayAbstractRequest
{
    protected $endpoint;

    public function getEndpoint(): string
    {
        if ($this->getTestMode()) {
            return 'https://poliapi.uat1.paywithpoli.com/' . ltrim($this->endpoint, '/');
        }

        return 'https://poliapi.apac.paywithpoli.com/' . ltrim($this->endpoint, '/');
    }
}
