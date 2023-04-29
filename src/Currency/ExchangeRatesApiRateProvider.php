<?php

namespace App\Currency;

class ExchangeRatesApiRateProvider implements RateProviderInterface
{
    public string $apiUrl = 'https://api.exchangeratesapi.io/latest';
    
    public function __construct(protected ?string $apiKey = null)
    { }

    public function getRate(string $from, string $to): float
    {
        if ($to != 'EUR') {
            throw new \RuntimeException(__CLASS__.' can provide rate only to EUR');
        }

        // why not?)
        if ($from == $to) {
            return 1;
        }

        $url = $this->apiUrl;
        if ($this->apiKey) {
            $url .= '?access_key='.$this->apiKey;
        }
        \var_dump($url);

        $response = \file_get_contents($url);
        if (!$response) {
            throw new \RuntimeException('Failed to load API '.$this->apiUrl);
        }
        $data = \json_decode($response, true);
        if (!$data || !$data['success'] || !isset($data['rates'])) {
            throw new \RuntimeException('Failed to load rates: '.$response);
        }
        
        if (!\array_key_exists($from, $data)) {
            throw new \RuntimeException('There is no rate for '.$from);
        }

        return $data[$from];
    }
}