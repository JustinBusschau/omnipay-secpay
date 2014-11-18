<?php

namespace Omnipay\SecPay;

use Omnipay\Common\AbstractGateway;

/**
 * SecPay Gateway
 *
 * @link http://www.paypoint.net/support/integration-guides/
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'SecPay (PayPoint.net)';
    }

    public function getDefaultParameters()
    {
        return array(
            'mid' => '',
            'vpnPswd' => '',
            'remotePswd' => '',
            'usageType' => '',
            'confirmEmail' => '',
            'testStatus' => 'true',
            'mailCustomer' => 'true',
            'additionalOptions' => ''
        );
    }

    public function getMid()
    {
        return $this->getParameter('mid');
    }

    public function setMid($value)
    {
        return $this->setParameter('mid', $value);
    }

    public function getVpnPswd()
    {
        return $this->getParameter('vpnPswd');
    }

    public function setVpnPswd($value)
    {
        return $this->setParameter('vpnPswd', $value);
    }

    public function getRemotePswd()
    {
        return $this->getParameter('remotePswd');
    }

    public function setRemotePswd($value)
    {
        return $this->setParameter('remotePswd', $value);
    }

    /**
     * A note re usage_type:
     * (M) = Mail Order / Telephone Order [aka MOTO]
     *        - Use this whenever the card holder is not present
     *          i.e. card details captured off a form or over the phone
     * (E) = eCommerce
     *        - Use this for any form where the car-holder directly captures
     *          their own card details, but NOT for repeating any of those transactions
     * (R) = Recurring
     *        - Use for any recurring (repeat / tokenized) payments
     *
     * According to SecPay tech support, you can either set this manually before submitting the transaction or you
     * can decide not to submit this option at all and the gateway will take a guess as to whether the transaction
     * should be M, E or R based on other data in the request. Apparently leaving it to the gateway to decide is
     * the safest option for PCI-DSS unless you are ABSOLUTELY sure you know what you're doing.
     */
    public function getUsageType()
    {
        return $this->getParameter('usageType');
    }

    public function setUsageType($value)
    {
        return $this->setParameter('usageType', $value);
    }

    public function getConfirmEmail()
    {
        return $this->getParameter('confirmEmail');
    }

    public function setConfirmEmail($value)
    {
        return $this->setParameter('confirmEmail', $value);
    }

    public function getTestStatus()
    {
        return $this->getParameter('testStatus');
    }

    public function setTestStatus($value)
    {
        return $this->setParameter('testStatus', $value);
    }

    public function getMailCustomer()
    {
        return $this->getParameter('mailCustomer');
    }

    public function setMailCustomer($value)
    {
        return $this->setParameter('mailCustomer', $value);
    }

    public function getAdditionalOptions()
    {
        return $this->getParameter('additionalOptions');
    }

    public function setAdditionalOptions($value)
    {
        return $this->setParameter('additionalOptions', $value);
    }

    public function authorize(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\AuthorizeRequest', $options);
    }

    public function capture(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\CaptureRequest', $options);
    }

    public function purchase(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\PurchaseRequest', $options);
    }

    public function refund(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\RefundRequest', $options);
    }

    public function void(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\VoidRequest', $options);
    }

    public function createCard(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\CreateCardRequest', $options);
    }

    public function report(array $options = array())
    {
        return $this->createRequest('\Omnipay\SecPay\Message\ReportRequest', $options);
    }
}
