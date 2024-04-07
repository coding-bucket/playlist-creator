<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands\Option;

use PlaylistCreator\Model\PlaylistIdentifier;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait PlaylistOptions
{
    public const ERROR_PLAYLIST_ID_AND_TITLE_SET = 'Either playlist ID or title must be set, not both.';
    private static string $ARG_NAME_PLAYLIST_ID = 'id';
    private static string $ARG_NAME_PLAYLIST_TITLE = 'title';

    abstract public function addOption(
        string $name,
        mixed  $shortcut = null,
        ?int   $mode = null,
        string $description = '',
        mixed  $default = null
    );

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException;
     */
    private function addPlaylistOptions(): self
    {
        $this->addOption(
            self::$ARG_NAME_PLAYLIST_ID,
            'l',
            InputOption::VALUE_REQUIRED,
            'The ID of an existing Youtube playlist.',
        )->addOption(
            self::$ARG_NAME_PLAYLIST_TITLE,
            'p',
            InputOption::VALUE_REQUIRED,
            'The title for a new Youtube playlist.',
        );

        return $this;
    }

    /**
     * @throws \InvalidArgumentException;
     */
    private function getPlaylistOptions(InputInterface $input): PlaylistIdentifier
    {
        $id = $this->getPlaylistIdArgument($input);
        $title = $this->getPlaylistTitleArgument($input);

        if (null !== $id && null !== $title) {
            throw new \InvalidArgumentException(self::ERROR_PLAYLIST_ID_AND_TITLE_SET);
        }

        return new PlaylistIdentifier($id, $title);
    }

    private function getPlaylistIdArgument(InputInterface $input): ?string
    {
        $id = $input->getOption(self::$ARG_NAME_PLAYLIST_ID);

        if (null !== $id && '' === trim((string) $id)) {
            throw new \InvalidArgumentException('Option ' . self::$ARG_NAME_PLAYLIST_ID . ' is empty.');
        }

        return $id;
    }

    private function getPlaylistTitleArgument(InputInterface $input): ?string
    {
        $title = $input->getOption(self::$ARG_NAME_PLAYLIST_TITLE);

        if (null !== $title && '' === trim((string) $title)) {
            throw new \InvalidArgumentException('Option ' . self::$ARG_NAME_PLAYLIST_TITLE . 'is empty.');
        }

        return $title;
    }
}
