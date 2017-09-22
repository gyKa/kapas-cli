<?php

require __DIR__ . '/../vendor/autoload.php';

use Command\CreateBookmarkCommand;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;

$config = new Dotenv\Dotenv(dirname(__DIR__));
$config->load();
$config->required('API_HOST')->notEmpty();

$application = new Application();
$client = new Client(['base_uri' => getenv('API_HOST')]);

$application->add(new CreateBookmarkCommand($client));

$application->run();
