<?php

namespace Zen\Payment;

/**
 * Class CartData
 *
 * @package Zen\Payment
 */
class CartData
{

    /**
     * @var array
     */
    private $items;

    /**
     * @var int
     */
    private $amount;

    /**
     * @param $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $textRound
     *
     * @return array
     */
    public function getCartData($textRound = '')
    {

        $total = 0;
        foreach ($this->items as $value) {

            $total += $value['lineAmountTotal'];

        }

        if ($total !== $this->amount) {

            $round = Util::convertAmount($this->amount - $total);

            $this->addItem(
                $textRound,
                $round,
                1,
                $round
            );
        }

        return $this->items;
    }

    /**
     * @param string $name
     * @param int $price
     * @param int $quantity
     * @param int $lineAmountTotal
     *
     * @return void
     */
    public function addItem($name, $price, $quantity, $lineAmountTotal)
    {

        $this->items[] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'lineAmountTotal' => $lineAmountTotal,
        ];
    }
}
