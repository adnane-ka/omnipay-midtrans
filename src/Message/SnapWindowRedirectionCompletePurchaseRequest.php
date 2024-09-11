<?php

namespace Omnipay\Midtrans\Message;


class SnapWindowRedirectionCompletePurchaseRequest extends AbstractRequest
{
    public function getEndPoint(){
        $orderId = $this->getParameter('order_id');

        return $this->getParameter('testMode') 
        ? "https://api.sandbox.midtrans.com/v2/{$orderId}/status"
        : "https://api.midtrans.com/v2/{$orderId}/status";
    }

    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }

    public function getData()
    {
        $this->validate('order_id');
    }

    public function sendData($data)
    {
        $responseData = $this->httpClient
            ->request('GET', $this->getEndPoint(), [
                'Authorization' => 'Basic ' . base64_encode($this->getServerKey() . ':')
            ] ,json_encode($data))
            ->getBody()
            ->getContents();

        return new SnapWindowRedirectionCompletePurchaseResponse(
            $this, $responseData
        );
    }
}