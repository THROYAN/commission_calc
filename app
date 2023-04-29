#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CalculateCommissionCommand;
use App\Country\BinLookupCountryProvider;
use App\Currency\ApiLayerRateProvider;
use App\Currency\ExchangeRatesApiRateProvider;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$application = new Application();

// @todo add dependency injection
// $rateProvider = new ExchangeRatesApiRateProvider($_ENV['EXCHANGE_RATES_API_KEY']);
// $rateProvider->apiUrl = 'https://api.apilayer.com/exchangerates_data/latest';
$rateProvider = new ApiLayerRateProvider(new Client, $_ENV['EXCHANGE_RATES_API_KEY']);
$application->add(new CalculateCommissionCommand(
    new Filesystem,
    new BinLookupCountryProvider,
    $rateProvider,
));

$application->run();
