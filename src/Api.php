<?php

namespace Zen\Payment;

/**
 * Class Api
 *
 * @package Zen\Payment
 */
class Api
{

    const TRANSACTIONS = 'transactions';
    const REFUND = 'refund';

    const ROUTE_PAY = '/api/checkouts';

    /**
     * @var array
     */
    private static $serviceUrls = [
        Util::ENVIRONMENT_PRODUCTION => 'https://secure.zen.com'
    ];

    /**
     * @var string
     */
    private $environment;

    /**
     * Api constructor.
     * @param string $environment
     */
    public function __construct($environment)
    {

        $this->environment = $environment;
    }

    /**
     * @param string $paymentData
     *
     * @return array
     */
    public function createPayment($paymentData)
    {
        return $this->call(
            $this->getTransactionCreateUrl(),
            Util::METHOD_REQUEST_POST,
            $paymentData,
            true
        );
    }

    /**
     * @param string $url
     * @param string $methodRequest
     * @param string $body
     * @param bool $decode
     *
     * @return array
     */
    private function call($url, $methodRequest, $body, $decode = false)
    {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $methodRequest);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $resultCurl = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (($httpCode >= 300) || !$resultCurl) {
            return [
                'success' => false,
                'data' => [
                    'httpCode' => $httpCode,
                    'error' => curl_error($curl),
                ],
            ];
        }

        if ($decode) {
            return [
                'success' => true,
                'body' => json_decode($resultCurl, true),
            ];
        }

        return [
            'success' => true,
            'body' => $resultCurl,
        ];
    }

    /**
     * @return string
     */
    private function getTransactionCreateUrl()
    {

        $baseUrl = self::getServiceUrl($this->environment);

        if ($baseUrl) {
            return $baseUrl . self::ROUTE_PAY;
        }

        return '';
    }

    /**
     * @param string $environment
     *
     * @return string
     */
    private static function getServiceUrl($environment)
    {

        if (isset(self::$serviceUrls[$environment])) {
            return self::$serviceUrls[$environment];
        }

        return '';
    }

    /**
     * @param string $environment
     * @param string $amount
     * @param string $transactionUuid
     * @param string $currency
     * @param string $orderId
     *
     * @return array
     */
    public function createRefund($amount, $transactionUuid, $currency, $orderId)
    {

        return $this->call(
            $this->getRefundCreateUrl(),
            Util::METHOD_REQUEST_POST,
            $this->prepareRefundData($amount, $transactionUuid, $currency, $orderId),
            true
        );
    }

    /**
     * @return string
     */
    private function getRefundCreateUrl()
    {

        $baseUrl = self::getServiceUrl($this->environment);

        if ($baseUrl) {
            return $baseUrl . '/' . self::TRANSACTIONS . '/' . self::REFUND;
        }

        return '';
    }

    /**
     * @param mixed $amount
     * @param string $transactionUuid
     * @param string $currency
     * @param string $orderId
     *
     * @return string
     */
    private function prepareRefundData(
        $amount,
        $transactionUuid,
        $currency,
        $orderId
    )
    {

        return json_encode([
            'amount' => (string)$amount,
            'transactionId' => $transactionUuid,
            'currency' => $currency,
            'merchantTransactionId' => $orderId . '#' . uniqid(),
        ]);
    }
}
