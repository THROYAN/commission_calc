<?php

namespace App\Commission;

use App\Country\CountryProviderInterface;
use App\Model\Transaction;

class CommissionProvider
{
    public function __construct(protected CountryProviderInterface $countryProvider)
    { }

    public function getRateForTransaction(Transaction $transaction): float
    {
        $country = $this->countryProvider->getCountryForTransaction($transaction);

        return $country->isEU() ? 0.01 : 0.02;
    }
}