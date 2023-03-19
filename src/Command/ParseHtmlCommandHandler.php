<?php

namespace App\Command;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ParseHtmlCommandHandler
{


    public function __invoke(ParseHtmlCommand $command): array
    {
        preg_match(
            $command->getRegexPattern(),
            $command->getHtmlBody(),
            $matches
        );

        return $matches;
    }

}