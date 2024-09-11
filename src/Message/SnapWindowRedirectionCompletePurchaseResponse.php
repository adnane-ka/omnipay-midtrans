<?php

namespace Omnipay\Midtrans\Message;

use Omnipay\Common\Message\AbstractResponse;

class SnapWindowRedirectionCompletePurchaseResponse extends AbstractResponse
{
    public function isPending()
    {
        return !$this->isSuccessful();
    }

    public function isSuccessful()
    {
        return $this->getData()['status_code'] == '200';
    }

    public function getTransactionReference()
    {
        return $this->getData()['transaction_id'];
    }
    
    public function getData(){
        return json_decode($this->data, true);
    }
}