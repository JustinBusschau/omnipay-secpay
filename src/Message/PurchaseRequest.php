<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $method = '';

    private function getNewCardData()
    {
        $this->validate('amount', 'card', 'description', 'transactionId');
        $card = $this->getCard();

        $requestData                    = $this->createBasicDataStructure();
        $requestData['trans_id']        = $this->getTransactionId();
        $requestData['amount']          = $this->getAmount();

        $requestData['expiry_date']     = $card->getExpiryDate('my');

        $requestData['name']            = $card->getName();
        $requestData['card_number']     = $card->getNumber();
        $requestData['issue_number']    = $card->getIssueNumber();
        $requestData['start_date']      = $card->getStartDate('my');
        $requestData['shipping']        = $this->buildAddress($card, 'Shipping');
        $requestData['billing']         = $this->buildAddress($card, 'Billing');

        $otherOptions                   = $this->createOptionStruct();
        $otherOptions['repeat']         = '';
        $otherOptions['cv2']            = $card->getCvv();
        $requestData['options']         = $this->buildOptionsQuery($otherOptions);

        return $requestData;
    }

    private function getReusedCardData()
    {
        $this->validate('amount', 'description', 'transactionId');

        $requestData                    = $this->createBasicDataStructure();
        $requestData['trans_id']        = $this->getTransactionReference();
        $requestData['amount']          = $this->getAmount();

        $requestData['new_trans_id']    = $this->getTransactionId();

        $otherOptions                   = $this->createOptionStruct();
        $otherOptions['repeat']         = 'true';
        $requestData['options']         = $this->buildOptionsQuery($otherOptions);

        return $requestData;
    }

    public function getData()
    {
        $transRef = $this->getTransactionReference();

        if (empty($transRef)) {
            return $this->getNewCardData();
        } else {
            return $this->getReusedCardData();
        }
    }

    public function send()
    {
        $this->sendData = $this->getData();
        $this->method = (empty($this->sendData['new_trans_id'])) ? 'validateCardFull': 'repeatCardFull';

        return parent::send();
    }
}
