<?php

namespace Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class CreateBookmarkCommand extends Command
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
            ->setName('bookmark:create')
            ->setDescription('Creates a new bookmark.')
            ->addArgument('title', InputArgument::REQUIRED)
            ->addArgument('url', InputArgument::REQUIRED)
            ->addArgument('tags', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, '', []);

    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $title = new Question('title: ');
        $url = new Question('url: ');
        $tags = new Question('tags: ');

        $title = $helper->ask($input, $output, $title);
        $url = $helper->ask($input, $output, $url);
        $tags = $helper->ask($input, $output, $tags);

        $input->setArgument('title', $title);
        $input->setArgument('url', $url);
        $input->setArgument('tags', $tags);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');
        $url = $input->getArgument('url');
        $tags = $input->getArgument('tags');

        if ($tags) {
            $tags = explode(',', $tags);
        }

        $body = [
            'title' => $title,
            'url' => $url,
            'tags' => $tags,
        ];

        $response = $this->client->request('POST', 'bookmark', ['json' => $body]);

        $output->writeln($response->getStatusCode());
        $output->writeln($response->getBody()->getContents());
    }
}
