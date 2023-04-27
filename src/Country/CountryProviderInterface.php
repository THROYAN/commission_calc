<?php

namespace App\Country;

use App\Model\Country;
use App\Model\Transaction;

interface CountryProviderInterface
{
    public function getCountryForTransaction(Transaction $transaction): Country;
}