<?php

namespace Omnipay\Midtrans\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

class SnapWindowRedirectionPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        if (!is_array($data)) {
            $this->data = json_decode(trim($data), true);
        }
    }

    public function isPending()
    {
        return !isset($this->data['token']);
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return isset($this->data['redirect_url']);
    }

    public function getRedirectUrl()
    {
        return isset($this->data['redirect_url']) ? $this->data['redirect_url'] : null;
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return null;
    }

    public function getMessage()
    {
        if (!isset($this->data['error_messages'])) {
            return null;
        }

        
        return implode("\n", $this->data['error_messages']);
    }
}