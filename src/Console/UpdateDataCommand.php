<?php

namespace App\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-data',
    description: 'Update currency table',
)]
class UpdateDataCommand  extends Command
{

    protected function configure(): void
    {
        $this
            ->setDescription('Update currency table');

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        echo "Update currency table".PHP_EOL;
            return Command::SUCCESS;

            return Command::FAILURE;
    }
}