<?php

namespace Omnipay\Midtrans\Message;


use Guzzle\Http\Exception\ClientErrorResponseException;
use Omnipay\Common\Exception\InvalidRequestException;

class SnapWindowRedirectionPurchaseRequest extends AbstractRequest
{
    const MIN_AMOUNT = 10000;
    const MAX_LENGTH_TRANSACTION_ID = 50;

    public function sendData($data)
    {
        $responseData = $this->httpClient
            ->request('POST', $this->getEndPoint(), $this->getSendDataHeader(), json_encode($data))
            ->getBody()
            ->getContents();

        return $this->response = new SnapWindowRedirectionPurchaseResponse($this, $responseData);
    }

    /**
     * @return array|mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate('amount', 'transactionId');

        $this->guardTransationId();

        $result = [
            'transaction_details' => [
                'order_id' => $this->getTransactionId(),
                'gross_amount' => (int) $this->getAmount(),
            ],
            'item_details' => [
                [
                    'id' => $this->getTransactionId(),
                    'price' => (int) $this->getAmount(),
                    'quantity' => 1,
                    'name' => $this->getDescription() ?? 'Product',
                    'brand' => $this->getDescription(),
                ]
            ],
            'credit_card' => [
                'secure' => true
            ],
            'callbacks' => [
                'finish' => $this->getReturnUrl()
            ]
        ];

        if ($this->getCard()) {
            $result['customer_details'] = [
                'first_name' => $this->getCard()->getFirstName(),
                'last_name' => $this->getCard()->getLastName(),
                'email' => $this->getCard()->getEmail(),
                'phone' => $this->getCard()->getNumber(),
            ];
        }

        return $result;
    }

    protected function getSendDataHeader()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->getServerKey() . ':')
        ];
    }

    /**
     * @throws InvalidRequestException
     */
    private function guardTransationId()
    {
        if (!preg_match('/^[a-z0-9\-_\~.]+$/i', $this->getTransactionId())) {
            throw new InvalidRequestException(
                'Allowed symbols for transactionId are dash(-), underscore(_), tilde (~), and dot (.)'
            );
        }

        if (strlen($this->getTransactionId()) > self::MAX_LENGTH_TRANSACTION_ID) {
            throw new InvalidRequestException(
                'Max length for transactionId is ' . self::MAX_LENGTH_TRANSACTION_ID
            );
        }

    }

}