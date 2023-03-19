<?php

namespace App\Command;

use App\Console\Htt;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AsMessageHandler]
class SendRequestCommandHandler
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendRequestCommand $command): ResponseInterface
    {
        return $this->client->request($command->getMethod(), $command->getUrl());
    }

}