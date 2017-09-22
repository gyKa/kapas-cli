<?php

require __DIR__ . '/../vendor/autoload.php';

use Command\CreateBookmarkCommand;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;

$application = new Application();
$client = new Client(['base_uri' => 'http://localhost:8000/']);

$application->add(new CreateBookmarkCommand($client));

$application->run();
