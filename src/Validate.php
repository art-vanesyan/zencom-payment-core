<?php

namespace Zen\Payment;

use Exception;
use JsonSchema\Validator;

/**
 * Class Validate
 *
 * @package Zen\Payment
 */
class Validate
{

    /**
     * @param string $data
     *
     * @return bool
     * @throws Exception
     */
    public static function notification($data)
    {

        $schema = [

            'type' => 'object',
            'properties' => [

                'amount' => [
                    'type' => 'string',
                ],
                'currency' => [
                    'type' => 'string',
                    'enum' => array_values(Util::getCurrencies()),
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => array_values(Util::getTransactionStatuses()),
                ],
                'merchantTransactionId' => [
                    'type' => 'string',
                ],
                'transactionId' => [
                    'type' => 'string',
                ],

            ],
            'required' => [
                'amount',
                'currency',
                'status',
                'merchantTransactionId',
                'transactionId',
            ],

        ];

        return self::validate($data, $schema, 'notification');
    }

    /**
     * @param string $data
     * @param array $schema
     * @param string $schemaType
     *
     * @return bool
     * @throws Exception
     */
    private static function validate($data, $schema, $schemaType)
    {

        $data = json_decode($data);

        $validator = new Validator();
        $validator->validate($data, json_decode(json_encode($schema)));

        if ($validator->isValid()) {
            return true;
        }

        $errors = [
            'schema' => $schemaType,
        ];

        foreach ($validator->getErrors() as $error) {
            $errors[$error['property']] = $error['message'];
        }

        throw new Exception(json_encode($errors));
    }
}
