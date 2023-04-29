<?php

namespace App\Country;

use App\Model\Country;
use App\Model\Transaction;
use GuzzleHttp\Client;

class BinLookupCountryProvider implements CountryProviderInterface
{
    public string $apiUrl = 'https://lookup.binlist.net/';

    public function __construct(protected Client $client)
    { }

    public function getCountryForTransaction(Transaction $transaction): Country
    {
        $response = $this->client->get($this->apiUrl.$transaction->getBin());
        if (!$response) {
            throw new \RuntimeException('Failed to lookup for bin '.$transaction->getBin());
        }
        $r = \json_decode($response->getBody()->getContents(), true);
        if (!$r || !isset($r['country']['name'], $r['country']['alpha2'])) {
            throw new \RuntimeException(\sprintf(
                'Failed to parse bin lookup response "%s" for bin %s',
                $response->getBody()->getContents(),
                $transaction->getBin()
            ));
        }

        return new Country($r['country']['alpha2'], $r['country']['name']);
    }
}