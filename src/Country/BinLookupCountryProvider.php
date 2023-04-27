<?php

namespace App\Country;

use App\Model\Country;
use App\Model\Transaction;

class BinLookupCountryProvider implements CountryProviderInterface
{
    public string $apiUrl = 'https://lookup.binlist.net/';

    public function getCountryForTransaction(Transaction $transaction): Country
    {
        $binResult = \file_get_contents($this->apiUrl.$transaction->getBin());
        if (!$binResult) {
            throw new \RuntimeException('Failed to lookup for bin '.$transaction->getBin());
        }
        $r = \json_decode($binResult, true);
        if (!$r || !isset($r['country']['name'], $r['country']['alpha2'])) {
            throw new \RuntimeException(\sprintf(
                'Failed to parse bin lookup response "%s" for bin %s',
                $binResult,
                $transaction->getBin()
            ));
        }

        return new Country($r['country']['alpha2'], $r['country']['name']);
    }
}