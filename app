#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CalculateCommissionCommand;
use App\Country\BinLookupCountryProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;

$application = new Application();

// @todo add dependency injection
$application->add(new CalculateCommissionCommand(
    new Filesystem,
    new BinLookupCountryProvider
));

$application->run();
