<?php

namespace Zen\Payment;

/**
 * Class Util
 *
 * @package Payment
 */
class Util
{

    const METHOD_REQUEST_POST = 'POST';
    const ENVIRONMENT_PRODUCTION = 'production';

    /**
     * @var array
     */
    private static $hashMethods = [
        'sha224' => 'sha224',
        'sha256' => 'sha256',
        'sha384' => 'sha384',
        'sha512' => 'sha512',
    ];

    /**
     * @var array
     */
    private static $transactionStatuses = [
        'CANCELED' => 'CANCELED',
        'AUTHORIZED' => 'AUTHORIZED',
        'PENDING' => 'PENDING',
        'REJECTED' => 'REJECTED',
        'ACCEPTED' => 'ACCEPTED',
    ];

    /**
     * Functions that return true when passed currency is on supported currencies list.
     *
     * @param string $currencyCode ISO4217
     * @param        $currencies
     *
     * @return bool
     */
    public static function canUseForCurrency($currencyCode, $currencies)
    {
        return in_array($currencyCode, $currencies);
    }

    /**
     * @param string $merchantTransactionId
     *
     * @return string
     */
    public static function extractMerchantTransactionId($merchantTransactionId)
    {

        return explode('#', $merchantTransactionId)[0];
    }

    /**
     * @return array
     */
    public static function getCurrencies()
    {
        return include 'Helpers/Currencies.php';
    }

    /**
     * @param string $variable
     *
     * @return bool
     */
    public static function isJson($variable)
    {
        json_decode($variable);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * @param string $hashMethod
     * @param string $data
     * @param string $serviceKey
     *
     * @return string
     */
    public static function hashSignature($hashMethod, $data, $serviceKey)
    {
        return hash($hashMethod, $data . $serviceKey);
    }

    /**
     * @param string $hashMethod
     *
     * @return string
     */
    public static function getHashMethod($hashMethod)
    {

        if (isset(self::$hashMethods[$hashMethod])) {
            return self::$hashMethods[$hashMethod];
        }

        return '';
    }

    /**
     * @return array
     */
    public static function getTransactionStatuses()
    {
        return self::$transactionStatuses;
    }

    /**
     * @param float $amount
     *
     * @return float
     */
    public static function convertAmount($amount)
    {
        return self::multiplyValues(round($amount, 2, PHP_ROUND_HALF_EVEN), 1, 2);
    }

    /**
     * @param number $firstValue
     * @param number $secondValue
     * @param number $precision
     *
     * @return float
     */
    public static function multiplyValues($firstValue, $secondValue, $precision)
    {
        return round($firstValue * $secondValue, $precision, PHP_ROUND_HALF_EVEN);
    }
}
