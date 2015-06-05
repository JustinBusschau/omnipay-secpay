<?php

namespace Omnipay\SecPay\Message;

use DOMDocument;
use SimpleXMLElement;
use Omnipay\SecPay\Gateway;

/**
 * SecPay Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $endpoint = 'https://www.secpay.com/java-bin/services/SECCardService';
    protected $namespace = 'http://secvpn.secpay.com';
    protected $method = '';

    protected $sendData = null;

    protected $requiredParameters = array(
        // 3D Secure Not Implemented but I'm leaving this in anyway
        'threeDSecureEnrolmentRequest' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'ip',
            'name',
            'card_number',
            'amount',
            'expiry_date',
            'issue_number',
            'start_date',
            'order',
            'shipping',
            'billing',
            'options',
            'device_category',
            'accept_headers',
            'user_agent',
            'mpi_merchant_name',
            'mpi_merchant_url',
            'mpi_description',
            'purchaseRecurringFrequency',
            'purchaseRecurringExpiry',
            'purchaseInstallments'
        ),
        // 3D Secure Not Implemented but I'm leaving this in anyway
        'threeDSecureAuthorisationRequest' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'md',
            'paRes',
            'options'
        ),
        'validateCardFull' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'ip',
            'name',
            'card_number',
            'amount',
            'expiry_date',
            'issue_number',
            'start_date',
            'order',
            'shipping',
            'billing',
            'options'
        ),
        'releaseCardFull' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'amount',
            'remote_pswd',
            'new_trans_id'
        ),
        'repeatCardFull' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'amount',
            'remote_pswd',
            'new_trans_id',
            'expiry_date',
            'order'
        ),
        'refundCardFull' => array(
            'mid',
            'vpn_pswd',
            'trans_id',
            'amount',
            'remote_pswd',
            'new_trans_id'
        ),
        'getReport' => array(
            'mid',
            'vpn_pswd',
            'remote_pswd',
            'report_type',
            'cond_type',
            'condition',
            'currency',
            'predicate',
            'html',
            'showErrs'
        )
    );

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

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getRequiredParameters($method)
    {
        return $this->requiredParameters[$method];
    }

    public function buildAddress($card, $type)
    {
        $address    = array(
            'name'      => call_user_func(array($card, "get{$type}Name")),
            'company'   => call_user_func(array($card, "get{$type}Company")),
            'addr_1'    => call_user_func(array($card, "get{$type}Address1")),
            'addr_2'    => call_user_func(array($card, "get{$type}Address2")),
            'city'      => call_user_func(array($card, "get{$type}City")),
            'state'     => call_user_func(array($card, "get{$type}State")),
            'post_code' => call_user_func(array($card, "get{$type}Postcode")),
            'country'   => call_user_func(array($card, "get{$type}Country")),
            'tel'       => call_user_func(array($card, "get{$type}Phone")),
        );
        if ($type == 'Billing') {
            $address['email'] = $card->getEmail();
        }

        return $this->buildOptionsQuery($address);
    }

    public function isMobileBrowser()
    {
        // I didn't want to install a full-featured library just to tell me whether the user_agent was mobile
        // or not, so I used this instead. If you need it and can be bothered, feel free to improve on this.
        // Source: http://detectmobilebrowsers.com/
        return (
            preg_match(
                '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop
|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|
phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|
xda|xiino/i',
                $this->httpRequest->headers->get('user_agent')
            ) ||
            preg_match(
                '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)
|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)
|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s
|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)
|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c
|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris
|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])
|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do
|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf
|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)
|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)
|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0
|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-
|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)
|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu
|x700|yas\-|your|zeto|zte\-/i',
                substr($this->httpRequest->headers->get('user_agent'), 0, 4)
            )
        );
    }

    public function createBasicDataStructure()
    {
        return array(
            'mid'                        => $this->getMid(),
            'vpn_pswd'                   => $this->getVpnPswd(),
            'trans_id'                   => $this->getTransactionId(),
            'ip'                         => $this->httpRequest->getClientIp(),
            'name'                       => '',
            'card_number'                => '',
            'amount'                     => '',
            'remote_pswd'                => $this->getRemotePswd(),
            'new_trans_id'               => '',
            'expiry_date'                => '',
            'issue_number'               => '',
            'start_date'                 => '',
            'order'                      => $this->getDescription(),
            'shipping'                   => '',
            'billing'                    => '',
            'options'                    => '',
            'device_category'            => ($this->isMobileBrowser()) ? '1' : '0',
            'accept_headers'             => $this->httpRequest->headers->get('accept'),
            'user_agent'                 => $this->httpRequest->headers->get('user_agent'),
            'mpi_merchant_name'          => '', // if left blank, the mpi_merchant_* fields will use
            'mpi_merchant_url'           => '', // values from the vendor's PayPoint.net profile
            'mpi_description'            => $this->getDescription(), // the cart/order
            'purchaseRecurringFrequency' => '',
            'purchaseRecurringExpiry'    => '',
            'purchaseInstallments'       => '',
        );
    }

    public function createOptionStruct()
    {
        // we'll be creating this with some default settings which can subsequently be overwritten as required
        $optionStruct = array(
            'repeat'               => '',
            'cv2'                  => '',
            'currency'             => $this->getCurrency(),
            'dups'                 => ($this->getTestStatus() === 'live') ? 'true' : 'false',
            'test_status'          => $this->getTestStatus(),
            'test_mpi_status'      => ($this->getTestStatus() === 'live') ? '' : 'true',
            'deferred'             => '',
            'usage_type'           => $this->getUsageType(), // see note in SecPay/Gateway.php
            'default_cv2avs'       => 'ALL MATCH', // The default should suffice for most test cases
            'mail_merchants'       => '+RDLPAFCSEMQN:'.$this->getConfirmEmail(), // notify merchant for EVERYTHING
            'mail_attach_merchant' => 'false',
            'mail_attach_customer' => 'false',
            'mail_customer'        => ($this->getMailCustomer() === 'true') ? '+FARDLPQN:bill' : 'false',
                                                               // for relevant events, notify the billing email address
            'mail_html'            => 'true',
            'mail_message'         => '',
        );

        $additionalOptions = $this->getAdditionalOptions();
        if (is_array($additionalOptions)) {
            $optionStruct = array_merge($optionStruct, $additionalOptions);
        }

        return $optionStruct;
    }

    public function buildOptionsQuery($options)
    {
        return urldecode(http_build_query(array_filter($options), '', ','));
    }

    public function prepareParameters($data)
    {
        $return = array();
        $required_parameters = $this->getRequiredParameters($this->method);
        foreach ($required_parameters as $key) {
            $return[$key] = $data[$key];
        }

        return $return;
    }

    public function createSOAPEnvelope($data)
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $envelope = $document->appendChild(
            $document->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope')
        );
        $envelope->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $envelope->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $ns = $document->createAttributeNS($this->namespace, 'sec:attr');

        // SecPay seems to want this empty soapenv:Header element
        $soapHeader = $envelope->appendChild($document->createElement('soapenv:Header'));
        $body = $envelope->appendChild($document->createElement('soapenv:Body'));

        $payload = $document->createElementNS($this->namespace, "sec:{$this->method}");
        $payload->setAttribute('soapenv:encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/');

        foreach ($data as $element_name => $element_value) {
            $child = $document->createElement($element_name, $element_value);

            // with the exception of two parameters in the report requests,
            // all parameters are expected to be of type xsd:string
            if ($element_name != 'html' and $element_name != 'showErrs') {
                $child->setAttribute('xsi:type', 'xsd:string');
            } else {
                $child->setAttribute('xsi:type', 'xsd:boolean');
            }

            $payload->appendChild($child);
        }

        $body->appendChild($payload);

        return $document->saveXML();
    }

    /**
    * A helper function to send our request to the endpoint
    */
    public function sendData($data)
    {
        if ($this->sendData === null) {
            $this->sendData = $this->getData();
        }

        $data = $this->createSOAPEnvelope(
            $this->prepareParameters(
                $this->sendData
            )
        );

        $headers = array(
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => $this->method
        );

        $httpResponse = $this->httpClient->post(
            $this->getEndpoint(),
            $headers,
            $data
        )->send();

        return $this->response = new Response($this, $httpResponse->getBody());
    }
}
