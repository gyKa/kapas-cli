<?php

namespace Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;


class GetBookmarksCommand extends Command
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
            ->setDescription('Gets all bookmarks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->client->request('GET', 'bookmark');
        $content = $response->getBody()->getContents();
        $rows = json_decode($content);
        $data = [];

        foreach ($rows as $row) {
            $titles = [];

            foreach ($row->tags as $tag) {
                $titles[] = $tag->title;
            }

            $data[] = [$row->id, $row->title, $row->url, join(',', $titles)];
        }

        $io->table(
            ['ID', 'Title', 'URL', 'Tags'],
            $data
        );
    }
}
