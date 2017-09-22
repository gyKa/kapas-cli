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

        $url = new Question('url: ');
        $url = $helper->ask($input, $output, $url);

        $titleDefault = $this->getTitle($url);
        $titleQuestion = sprintf('Use this title? [%s]: ', $titleDefault);

        $title = new ConfirmationQuestion($titleQuestion, false);
        $title = $helper->ask($input, $output, $title);

        if (!$title) {
            $title = new Question('title: ', $titleDefault);
            $title = $helper->ask($input, $output, $title);
        }

        $tags = new Question('tags: ');
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

    /**
     * @param string $url
     * @return string
     */
    private function getTitle(string $url)
    {
        $urlContents = file_get_contents($url);

        $dom = new DOMDocument();
        @$dom->loadHTML($urlContents);

        $title = $dom->getElementsByTagName('title');

        return $title->item(0)->nodeValue;
    }
}
