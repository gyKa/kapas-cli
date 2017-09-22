<?php

namespace Command;

use DOMDocument;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;


class DeleteBookmarkCommand extends Command
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('bookmark:delete')
            ->setDescription('Deletes a specified bookmark.')
            ->addArgument('id', InputArgument::REQUIRED, 'bookmark ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');

        $response = $this->client->request('DELETE', join('/', ['bookmark', $id]));

        $output->writeln($response->getStatusCode());
    }
}
