<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands\Option;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait InteractiveOption
{
    private static string $ARG_NAME_INTERACTIVE = 'interactive';

    abstract public function addOption(
        string $name,
        mixed $shortcut = null,
        ?int $mode = null,
        string $description = '',
        mixed $default = null
    );

    protected function isInteractive(InputInterface $input): bool
    {
        return (bool) $input->getOption(self::$ARG_NAME_INTERACTIVE);
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException;
     */
    private function addInteractiveOption(): self
    {
        $this->addOption(
            self::$ARG_NAME_INTERACTIVE,
            'i',
            InputOption::VALUE_NONE,
            'Ask before adding found song to playlist.',
        );

        return $this;
    }
}
