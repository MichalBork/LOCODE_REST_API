<?php

namespace App\Service;

use App\DTO\CodeFunctionDTO;
use App\Entity\CodeFunction;
use App\Repository\CodeFunctionRepository;
use App\Repository\LocodeRepository;
use App\Entity\Locode;

class UpdateDataService
{

    private LocodeRepository $locodeRepository;
    private CodeFunctionRepository $codeFunctionRepository;
    private ParseFileDataService $parseFileDataService;
    private DownloadFilesService $downloadFilesService;


    public function __construct(LocodeRepository $locodeRepository, CodeFunctionRepository $codeFunctionRepository)
    {
        $this->locodeRepository = $locodeRepository;
        $this->codeFunctionRepository = $codeFunctionRepository;
        $this->parseFileDataService = new ParseFileDataService($this);
        $this->downloadFilesService = new DownloadFilesService($this);
    }


    public function updateLocode(array $csv): void
    {
        foreach ($csv as $value) {
            $data = $this->parseFileDataService->createArrayWithKeyAndCorrectEncoding($value);
            if (!empty($data['changeIndicator'])) {
                $locode = $this->locodeRepository->findOneBy(['locode' => $data['locode']]);
                if ($locode) {
                    foreach ($data as $key => $item) {
                        $locode->{'set' . ucfirst($key)}($item);

                        $this->locodeRepository->save($locode, true);
                    }
                }
            }
        }
    }

    /**
     * @param mixed $value
     * @return void
     */
    public
    function fillDatabaseWithLocode(
        mixed $value
    ): void {
        $data = $this->parseFileDataService->createArrayWithKeyAndCorrectEncoding($value);

        $this->newLocode($data);
    }


    /**
     * @param array $data
     * @return void
     */
    public
    function newLocode(
        array $data
    ): void {
        $locode = new Locode(
            $data['changeIndicator'],
            $data['locode'],
            $data['name'],
            $data['nameWoDiacritics'],
            $data['subdivision'],
            $data['status'],
            $data['date'],
            $data['iata'],
            $data['coordinates'],
            $data['remarks']
        );

        if (!empty($data['function'])) {
            $locode->addCodeFunction($this->parseFileDataService->parseFunctionColumn($data['function'], $locode));
        }


        $this->locodeRepository->save($locode, true);
    }


}