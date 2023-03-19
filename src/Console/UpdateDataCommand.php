<?php

namespace App\Console;

use App\Command\ParseHtmlCommand;
use App\Command\SendRequestCommand;
use App\Service\UpdateDataService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ZipArchive;

#[AsCommand(
    name: 'app:update-data',
    description: 'Update UNLOCEDE table',
)]
class UpdateDataCommand extends Command
{
    use HandleTrait;

    const URL = 'https://unece.org/trade/cefact/UNLOCODE-Download';
    const REGEX_FILE = '/https:\/\/service.unece.org\/trade\/locode\/(\w+)csv.zip/';
    const REGEX_DATE = '/<span class="field-content">(.+?)<\/span>/';
    const FILE_NAME = 'data.zip';
    const FILE_NAME_UNZIP = '/data';
    const FILE_PATH = __DIR__ . '/../../tmp';

    const METHOD_GET = 'GET';

    private HttpClientInterface $client;
    private MessageBusInterface $commandBus;
    private UpdateDataService $updateDataService;

    public function __construct(
        HttpClientInterface $client,
        MessageBusInterface $commandBus,
        MessageBusInterface $messageBus,
        UpdateDataService $updateDataService
    ) {
        parent::__construct();
        $this->client = $client;
        $this->commandBus = $commandBus;
        $this->messageBus = $messageBus;
        $this->updateDataService = $updateDataService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Update currency table');

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!file_exists(self::FILE_PATH)) {
            mkdir(self::FILE_PATH, 0777, true);
        }

        $findUrlToDownloadFile = $this->handle(new SendRequestCommand(self::METHOD_GET, self::URL));


        $dateOfLastUpdate = $this->handle(new ParseHtmlCommand($findUrlToDownloadFile->getContent(), self::REGEX_DATE));

        if ($this->updateDataService->checkIfUpdateIsNeeded($dateOfLastUpdate[1])) {
            $urlToDownloadFile = $this->handle(
                new ParseHtmlCommand($findUrlToDownloadFile->getContent(), self::REGEX_FILE)
            );
            file_put_contents(
                self::FILE_PATH . self::FILE_NAME,
                $this->handle(new SendRequestCommand(self::METHOD_GET, $urlToDownloadFile[0]))->getContent()
            );

            $this->updateDataService->unzipFile(
                self::FILE_PATH . self::FILE_NAME,
                self::FILE_PATH . self::FILE_NAME_UNZIP
            );


            $this->updateDataService->parseCsvFile(self::FILE_PATH . self::FILE_NAME_UNZIP);
            die();


            $this->updateDataService->createFileWithDate($dateOfLastUpdate[1]);
        } else {
            $output->writeln('Data is up to date');
            return Command::SUCCESS;
        }


        die();


        //add regex to get url


//        file_put_contents('data.zip', $file->getContent());
//
//        $zip = new ZipArchive();
//
//        $file = 'data.zip';
//
//        if ($zip->open($file) === true) {
//            $zip->extractTo('plik');
//        }
//            // Zamknij plik ZIP
//            $zip->close();
//
//        $csv = array_map('str_getcsv', file('plik/2022-2 UNLOCODE CodeListPart1.csv'));
//        var_dump($csv);
        die();
        return Command::SUCCESS;

        return Command::FAILURE;
    }
}