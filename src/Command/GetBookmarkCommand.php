<?php

namespace Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class GetBookmarkCommand extends Command
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
            ->setName('bookmark:get')
            ->setDescription('Gets specified bookmark.')
            ->addArgument('id', InputArgument::REQUIRED, 'bookmark ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');

        $response = $this->client->request('GET', join('/', ['bookmark', $id]));
        $content = $response->getBody()->getContents();
        $row = json_decode($content);

        $titles = [];

        foreach ($row->tags as $tag) {
            $titles[] = $tag->title;
        }

        $output->writeln('Title: ' . $row->title);
        $output->writeln('URL: ' . $row->url);
        $output->writeln('Tags: ' . join(',', $titles));
    }
}
