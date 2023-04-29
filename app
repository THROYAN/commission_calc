#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CalculateCommissionCommand;
use App\Commission\CommissionProvider;
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
$httpClient = new Client;
$application->add(new CalculateCommissionCommand(
    new Filesystem,
    new CommissionProvider(new BinLookupCountryProvider($httpClient)),
    new ApiLayerRateProvider($httpClient, $_ENV['EXCHANGE_RATES_API_KEY']),
));

$application->run();
