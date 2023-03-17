<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDataCommand  extends Command
{

    protected function configure(): void
    {
        $this
            ->setDescription('Update currency table');

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        echo "Update currency table";
            return Command::SUCCESS;

            return Command::FAILURE;
    }
}