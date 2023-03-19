<?php

namespace App\Command;

class ParseHtmlCommand
{

    private string $htmlBody;
    private string $regexPattern;

    public function __construct(string $url, string $regexPattern)
    {
        $this->htmlBody = $url;
        $this->regexPattern = $regexPattern;
    }

    public function getHtmlBody(): string
    {
        return $this->htmlBody;
    }

    public function getRegexPattern(): string
    {
        return $this->regexPattern;
    }
}