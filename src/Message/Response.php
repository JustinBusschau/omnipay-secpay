<?php

namespace Omnipay\SecPay\Message;

use DOMDocument;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * SecPay Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    protected $responseVerbiage = array(
        'A'   => 'Transaction authorised by bank. Auth Code: ', // 'auth_code available as bank reference.',
        'N'   => 'The payment was not authorised, but no reason was given by the bank.',
        'C'   => 'There has been a problem communicating with your bank.  Please try again later.',
        'F'   => 'The payment system has detected a possible fraud.  Please contact your bank.',
        'P:A' => 'The payment was not authorised as there an amount was not provided for payment.',
        'P:X' => 'The payment was not authorised as not all mandatory parameters were supplied.',
        'P:P' => 'The payment was not authorised as the same payment was presented twice.',
        'P:S' => 'The payment was not authorised as the start date was invalid.',
        'P:E' => 'The payment was not authorised as the expiry date was invalid.',
        'P:I' => 'The payment was not authorised as the issue number was invalid.',
        'P:C' => 'The payment was not authorised as the card number was incorrect.',
        'P:T' => 'The payment was not authorised as the card type does not match the card number.',
        'P:N' => 'The payment was not authroised as the card holder name was not supplied properly.',
        'P:M' => 'The payment was not authorised as the merchant does not exist or not registered yet.',
        'P:B' => 'The payment was not authorised as the merchant account for card type does not exist.',
        'P:D' => 'The payment was not authorised as the merchant account for this currency does not exist.',
        'P:V' =>
            'The payment was not authorised as the CV2 security code is mandatory and was not supplied or was invalid.',
        'P:R' =>
            'The payment was not authorised as the transaction timed out awaiting a virtual circuit. \
            Merchant may not have enough virtual circuits for the volume of business.',
        'P:#' => 'The payment was not authorised as there was no MD5 hash / token key set up against account.'
    );

    public function __construct(RequestInterface $request, $data)
    {
        $responseDom = new DOMDocument;
        $responseDom->loadXML((string)$data);
        $returnData = simplexml_import_dom($responseDom->documentElement->firstChild->firstChild);

        $returnNodeName = preg_replace('/Response$/', 'Return', $returnData->getName());

        parse_str(str_replace('?', '', (string)$returnData->$returnNodeName), $parsed);

        parent::__construct($request, $parsed);
    }

    public function isSuccessful()
    {
        return $this->data['valid'] === 'true';
    }

    public function isRedirect()
    {
        return isset($this->data['mpi_status_code']) and (int)$this->data['mpi_status_code'] === 200;
    }

    public function getTransactionReference()
    {
        return $this->data['trans_id'];
    }

    public function getMessage()
    {
        if (isset($this->data['code']) and isset($this->responseVerbiage[$this->data['code']])) {
            $auth_code = ($this->data['code'] == 'A') ? $this->data['auth_code'] : '';
            return $this->responseVerbiage[$this->data['code']].$auth_code;
        } else {
            if (!empty($this->data['message'])) {
                return (string)$this->data['message'];
            }
        }
        return '';
    }

    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return (string)$this->data['acs_url'];
        }
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->data;
    }
}
