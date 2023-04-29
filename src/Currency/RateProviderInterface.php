<?php

namespace App\Currency;

interface RateProviderInterface
{
    public function getRate(string $from, string $to): float;
}