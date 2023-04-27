#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\CalculateCommissionCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new CalculateCommissionCommand());

$application->run();
