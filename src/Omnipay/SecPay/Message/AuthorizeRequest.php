<?php

namespace Omnipay\SecPay\Message;

/**
 * SecPay Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{
    protected $method = 'validateCardFull';

    public function getData()
    {
        $this->validate('amount', 'card', 'description', 'transactionId');

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
        $otherOptions['cv2']            = $card->getCvv();
        $otherOptions['deferred']       = 'reuse'; // I will not be coding for the option to alter the auth period
        $requestData['options']         = $this->buildOptionsQuery($otherOptions);

        return $requestData;
    }
}
