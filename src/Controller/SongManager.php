<?php

declare(strict_types=1);

namespace PlaylistCreator\Controller;

use PlaylistCreator\Interface\Client;
use PlaylistCreator\Model\SearchResult;
use PlaylistCreator\Model\Song;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final readonly class SongManager
{
    public function __construct(
        private Client          $youtubeClient,
        private InputInterface  $input,
        private OutputInterface $output,
        private QuestionHelper  $helper
    ) {
    }

    public function addToPlaylist(Song $song, string $playlistId, bool $interactive): bool
    {
        $this->output->write((string) $song);

        $searchResult = $this->search($song);
        if (null === $searchResult) {
            return false;
        }

        if ($this->isRejected($searchResult->title, $searchResult->id, $interactive)) {
            return false;
        }

        return $this->add($searchResult, $playlistId);
    }

    private function search(Song $song): SearchResult|null
    {
        $searchResult = null;
        try {
            $searchResult = $this->youtubeClient->searchVideoId((string) $song);
            if (null === $searchResult) {
                $this->output->writeln(' -- <fg=red>not found</>');
                return null;
            }
        } catch (\Exception $e) {
            $this->output->writeln(' -- <fg=red>error searching id</>');
        }
        return $searchResult;
    }

    private function isRejected(string $title, string $id, bool $interactive): bool
    {
        if (!$interactive) {
            $this->output->write(' -- adding "' . $title . '" to playlist... ');
            return false;
        }

        $this->output->write(' -- found "' . $title . '" ( https://www.youtube.com/v?=' . $id . ' )');
        $question = new ConfirmationQuestion(' -- add (y/n)?');
        $toAdd = $this->helper->ask($this->input, $this->output, $question);

        return !(bool) $toAdd;
    }

    private function add(SearchResult $searchResult, string $playlistId): bool
    {
        try {
            $this->youtubeClient->insertInPlaylist($searchResult->id, $playlistId);
        } catch (\Exception $e) {
            $this->output->writeln(' -- <fg=red>error adding to playlist</>');
            return false;
        }

        $this->output->writeln('<fg=green>done</>');
        return true;
    }
}
