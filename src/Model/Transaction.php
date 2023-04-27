<?php

namespace App;

class Transaction
{
    public function __construct(
        protected string $bin,
        protected float $amount,
        protected string $currency,
    ) { }

    public static function fromJSON($json)
    {
        $result = \json_decode($json, true);
        if (!isset($result['bin'], $result['amount'], $result['currency'])) {
            throw new \RuntimeException('Can\'t create transaction from json: '.$json);
        }

        return new static($result['bin'], $result['amount'], $result['currency']);
    }
}