<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $method = '';

    public function getData()
    {
        $this->validate('amount', 'card', 'description', 'transactionId');

        $card = $this->getCard();

        $requestData                        = $this->createBasicDataStructure();
        $transRef                           = $this->getTransactionReference();
        $requestData['trans_id']            = (empty($transRef)) ? $this->getTransactionId() : $transRef;
        $requestData['amount']              = $this->getAmount();

        $requestData['expiry_date']         = $card->getExpiryDate('my');

        if (empty($transRef)) {
            $requestData['name']            = $card->getName();
            $requestData['card_number']     = $card->getNumber();
            $requestData['issue_number']    = $card->getIssueNumber();
            $requestData['start_date']      = $card->getStartDate('my');
            $requestData['shipping']        = $this->buildAddress($card, 'Shipping');
            $requestData['billing']         = $this->buildAddress($card, 'Billing');
        } else {
            $requestData['new_trans_id']    = $this->getTransactionId();
        }

        $otherOptions                       = $this->createOptionStruct();
        $otherOptions['repeat']             = (empty($transRef)) ? '' : 'true';
        $otherOptions['cv2']                = $card->getCvv();
        $requestData['options']             = $this->buildOptionsQuery($otherOptions);

        return $requestData;
    }

    public function send()
    {
        $this->sendData = $this->getData();
        $this->method = (empty($this->sendData['new_trans_id'])) ? 'validateCardFull': 'repeatCardFull';

        return parent::send();
    }
}
