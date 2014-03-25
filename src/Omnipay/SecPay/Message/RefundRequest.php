<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Refund Request
 */
class RefundRequest extends AbstractRequest
{
    protected $method = 'refundCardFull';

    public function getData()
    {
        $this->validate('amount', 'transactionId', 'transactionReference');

        $requestData                    = $this->createBasicDataStructure();
        $requestData['trans_id']        = $this->getTransactionReference();
        $requestData['amount']          = $this->getAmount();

        $requestData['new_trans_id']    = $this->getTransactionId();

        $requestData['options']         = $this->buildOptionsQuery($this->createOptionStruct());

        return $requestData;
    }
}
