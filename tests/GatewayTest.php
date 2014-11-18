<?php

namespace Omnipay\SecPay;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMid('secpay');
        $this->gateway->setVpnPswd('secpay');
        $this->gateway->setRemotePswd('secpay');
        $this->gateway->setUsageType('E');
        $this->gateway->setTestStatus('true');

        $this->authorizeOptions = array(
            'amount' => 123.45,
            'description' => 'Authorize Test',
            'card' => $this->getValidCard()
        );

        $this->captureOptions = array(
            'amount' => 123.45,
            'description' => 'Capture Test'
        );

        $this->createCardOptions = array(
            'amount' => 1.00,
            'description' => 'Create Card Test',
            'card' => $this->getValidCard()
        );

        $this->purchaseOptions = array(
            'amount' => 123.45,
            'description' => 'Purchase Test',
            'card' => $this->getValidCard()
        );

        $this->refundOptions = array(
            'amount' => 123.45,
            'description' => 'Refund Test',
            'card' => $this->getValidCard()
        );

        $this->voidOptions = array(
            'amount' => 123.45,
            'description' => 'Void Test',
            'card' => $this->getValidCard()
        );

        $this->mcc6012Options = array(
            'fin_serv_birth_date' => '19640225',
            'fin_serv_surname' => 'ONuill',
            'fin_serv_postcode' => 'NN5',
            'fin_serv_account' => '12345ABCDE'
        );
    }

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        $options = $this->authorizeOptions;
        $options['transactionId'] = 'AUTHSUCCESS01';
        $response = $this->gateway->authorize($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('AUTHSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testAuthorizeFailure()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');

        $options = $this->authorizeOptions;
        $options['transactionId'] = 'AUTHFAILURE01';
        $options['card']['expiryYear'] = '2000';
        $response = $this->gateway->authorize($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('AUTHFAILURE01', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised as the expiry date was invalid.', $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');

        $options = $this->captureOptions;
        $options['transactionId'] = 'CAPTSUCCESS01';
        $options['transactionReference'] = 'AUTHSUCCESS01';
        $response = $this->gateway->capture($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('CAPTSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');

        $options = $this->captureOptions;
        $options['transactionId'] = 'CAPTFAILURE01';
        $options['transactionReference'] = 'AUTHFAILURE01';
        $response = $this->gateway->capture($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('CAPTFAILURE01', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised, but no reason was given by the bank.', $response->getMessage());
    }

    public function testCreateCardSuccess()
    {
        $this->setMockHttpResponse('CreateCardSuccess.txt');

        $options = $this->createCardOptions;
        $options['transactionId'] = 'CUSTCC01';
        $response = $this->gateway->createCard($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('CUSTCC01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testCreateCardFailure()
    {
        $this->setMockHttpResponse('CreateCardFailure.txt');

        $options = $this->createCardOptions;
        $options['transactionId'] = 'CUSTCC02';
        $options['card']['expiryYear'] = '2000';
        $response = $this->gateway->createCard($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('CUSTCC02', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised as the expiry date was invalid.', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $options = $this->purchaseOptions;
        $options['transactionId'] = 'PURCHSUCCESS01';
        $response = $this->gateway->purchase($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('PURCHSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');

        $options = $this->purchaseOptions;
        $options['transactionId'] = 'PURCHFAILURE01';
        $options['card']['expiryYear'] = '2000';
        $response = $this->gateway->purchase($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('PURCHFAILURE01', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised as the expiry date was invalid.', $response->getMessage());
    }

    public function testPurchaseWithSavedCardSuccess()
    {
        $this->setMockHttpResponse('RepeatSuccess.txt');

        $options = $this->purchaseOptions;
        $options['transactionId'] = 'PURCHSAVED01';
        $options['transactionReference'] = 'REPEATSUCCESS01';
        $options['card'] = array();
        $options['description'] = 'Purchase with card re-use';
        $response = $this->gateway->purchase($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('REPEATSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testPurchaseWithSavedCardFailure()
    {
        $this->setMockHttpResponse('RepeatFailure.txt');

        $options = $this->purchaseOptions;
        $options['transactionId'] = 'PURCHSAVED01';
        $options['transactionReference'] = 'REPEATFAILURE01';
        $options['card'] = array();
        $options['description'] = 'Purchase with card re-use';
        $response = $this->gateway->purchase($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('REPEATFAILURE01', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised as the expiry date was invalid.', $response->getMessage());
    }

    public function testRefundSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $options = $this->refundOptions;
        $options['transactionId'] = 'REFUNDSUCCESS01';
        $options['transactionReference'] = 'REFUNDTRXN01';
        $response = $this->gateway->refund($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('REFUNDSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    public function testRefundFailure()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $options = $this->refundOptions;
        $options['transactionId'] = 'REFUNDFAILURE02';
        $options['transactionReference'] = 'REFUNDTRXN99';
        $response = $this->gateway->refund($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('REFUNDFAILURE02', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised, but no reason was given by the bank.', $response->getMessage());
    }

    public function testVoidSuccess()
    {
        $this->setMockHttpResponse('VoidSuccess.txt');

        $options = $this->voidOptions;
        $options['transactionId'] = 'VOIDTRXN01';
        $options['transactionReference'] = 'AUTHSUCCESSV1';
        $response = $this->gateway->void($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('VOIDTRXN01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: ', $response->getMessage());
    }

    public function testVoidFailure()
    {
        $this->setMockHttpResponse('VoidFailure.txt');

        $options = $this->voidOptions;
        $options['transactionId'] = 'VOIDTRXN02';
        $options['transactionReference'] = 'AUTHSUCCESSV2';
        $response = $this->gateway->void($options)->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('VOIDTRXN02', $response->getTransactionReference());
        $this->assertSame('The payment was not authorised, but no reason was given by the bank.', $response->getMessage());
    }

    public function testMcc6012Options()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $options = $this->purchaseOptions;
        $options['transactionId'] = 'PURCHSUCCESS01';

        $this->gateway->setAdditionalOptions($this->mcc6012Options);
        $this->assertSame(
            $this->gateway->getAdditionalOptions(),
            $this->mcc6012Options
        );

        $response = $this->gateway->purchase($options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('PURCHSUCCESS01', $response->getTransactionReference());
        $this->assertSame('Transaction authorised by bank. Auth Code: 9999', $response->getMessage());
    }

    // TODO: Add tests for 3DS.
}
