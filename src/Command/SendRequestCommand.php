<?php

namespace App\Command;

class SendRequestCommand
{
    private string $method;
    private string $url;


    public function __construct(string $method, string $url)
    {
        $this->method = $method;
        $this->url = $url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }


}