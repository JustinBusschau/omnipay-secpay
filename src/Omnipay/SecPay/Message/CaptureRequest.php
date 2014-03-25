<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Capture Request
 */
class CaptureRequest extends AbstractRequest
{
    protected $method = 'releaseCardFull';

    public function getData()
    {
        $this->validate('amount', 'transactionId', 'transactionReference');

        $requestData                    = $this->createBasicDataStructure();
        $requestData['trans_id']        = $this->getTransactionReference();
        $requestData['amount']          = $this->getAmount();

        $requestData['new_trans_id']    = $this->getTransactionId();

        return $requestData;
    }
}
