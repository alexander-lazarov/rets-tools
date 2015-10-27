#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use RetsTools\Command\TestConnectionCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new TestConnectionCommand());
$application->run();
