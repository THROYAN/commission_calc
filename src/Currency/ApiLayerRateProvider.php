<?php

namespace App\Currency;

use GuzzleHttp\Client;

class ApiLayerRateProvider implements RateProviderInterface
{
    // would make apiUrl and endpoints separately, but not now
    // really would make ApiLayerApiClient and use it here to call latest method (and there are specific methods for get specific rate...)
    public string $apiUrl = 'https://api.apilayer.com/exchangerates_data/latest';

    private ?array $latestCache = null;
    
    public function __construct(protected Client $client, protected string $apiKey)
    { }

    public function getRate(string $from, string $to): float
    {
        // domain specific...)
        if ($from != 'EUR') {
            throw new \RuntimeException(__CLASS__.' can provide rate only to EUR');
        }

        // why not?)
        if ($from == $to) {
            return 1;
        }

        if ($this->latestCache != null && array_key_exists($to, $this->latestCache)) {
            return $this->latestCache[$to];
        }

        $response = $this->client->get($this->apiUrl, ['headers' => ['apikey' => $this->apiKey]]);

        if (!$response) {
            throw new \RuntimeException('Failed to load API '.$this->apiUrl);
        }
        $data = \json_decode($response->getBody()->getContents(), true);
        if (!$data || !$data['success'] || !isset($data['rates'])) {
            throw new \RuntimeException('Failed to load rates: '.$response->getBody()->getContents());
        }
        
        if (!\array_key_exists($to, $data['rates'])) {
            throw new \RuntimeException('There is no rate for '.$to.'. Available rates: '.implode(', ', array_keys($data['rates'])));
        }
        // save cache
        $this->latestCache = $data['rates'];
 
        return $data['rates'][$to];
    }
}