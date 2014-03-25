<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Void Request
 */
class VoidRequest extends AbstractRequest
{
    protected $method = 'releaseCardFull';

    public function getData()
    {
        $this->validate('amount', 'transactionId', 'transactionReference');

        $requestData                    = $this->createBasicDataStructure();
        $requestData['trans_id']        = $this->getTransactionReference();
        $requestData['amount']          = '-1';

        $requestData['options']         = $this->buildOptionsQuery($this->createOptionStruct());

        return $requestData;
    }
}
