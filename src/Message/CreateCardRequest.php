<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay CreateCard Request
 */
class CreateCardRequest extends AbstractRequest
{
    protected $method = 'validateCardFull';

    public function getData()
    {
        $this->validate('card', 'description', 'transactionId');

        $card = $this->getCard();

        $requestData                    = $this->createBasicDataStructure();
        $requestData['name']            = $card->getName();
        $requestData['card_number']     = $card->getNumber();
        $requestData['amount']          = $this->getAmount();

        $requestData['expiry_date']     = $card->getExpiryDate('my');
        $requestData['issue_number']    = $card->getIssueNumber();
        $requestData['start_date']      = $card->getStartDate('my');

        $requestData['shipping']        = $this->buildAddress($card, 'Shipping');
        $requestData['billing']         = $this->buildAddress($card, 'Billing');

        $otherOptions                   = $this->createOptionStruct();
        $otherOptions['deferred']       = 'true';
        $otherOptions['repeat']         = 'true';
        $otherOptions['cv2']            = $card->getCvv();
        $requestData['options']         = $this->buildOptionsQuery($otherOptions);

        return $requestData;
    }
}
