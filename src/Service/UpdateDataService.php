<?php

namespace App\Service;

use App\Repository\LocodeRepository;
use App\Entity\Locode;

class UpdateDataService
{
    const FILE_NAME = 'lastUpdateDate.txt';
    const FILE_PATH = __DIR__ . '/../../' . self::FILE_NAME;
    const COLUMN_NAMES = [
        'changeIndicator',
        'locode',
        'name',
        'nameWoDiacritics',
        'subdivision',
        'function',
        'subdivision',
        'status',
        'date',
        'iata',
        'coordinates',
        'remarks'
    ];
    private LocodeRepository $locodeRepository;


    public function __construct(LocodeRepository $locodeRepository)
    {
        $this->locodeRepository = $locodeRepository;
    }

    public function checkIfUpdateIsNeeded(string $updateDate): bool
    {
        if (!file_exists(self::FILE_PATH)) {
            $this->createFileWithDate($updateDate);
            return true;
        }
        return !$this->checkLastUpdateDate($updateDate);
    }


    private function checkLastUpdateDate(string $updateDate): bool
    {
        file_get_contents(self::FILE_PATH);
        return $updateDate === file_get_contents(self::FILE_PATH);
    }


    private function saveLastUpdateDate(string $lastUpdateDate): void
    {
        file_exists(self::FILE_PATH) ? unlink(self::FILE_PATH) : null;
        file_put_contents(self::FILE_PATH, $lastUpdateDate);
    }

    public function createFileWithDate(string $lastUpdateDate): void
    {
        $this->saveLastUpdateDate($lastUpdateDate);
    }

    public function unzipFile(string $file, string $destination): void
    {
        $zip = new \ZipArchive();
        $zip->open($file);
        $zip->extractTo($destination);
        $zip->close();
        unlink($file);
    }


    public function parseCsvFile(string $pathToFile)
    {
        $data = [];
$x = 0;
        $csv = array_map('str_getcsv', file($pathToFile . '/2022-2 UNLOCODE CodeListPart1.csv'));
        $lastKey = array_key_last($csv);
        foreach ($csv as $key => $value) {
            $data1 = array_combine(self::COLUMN_NAMES, $value);
            $data = array_map('addslashes', $data1);
            if ($data1['nameWoDiacritics'] !== $data1['nameWoDiacritics']){
                dd($data1,$data);
            }
            try {
                $locode = new Locode(
                    $data['changeIndicator'],
                    $data['locode'],
                    $data['name'],
                    $data['nameWoDiacritics'],
                    $data['subdivision'],
                    $data['function'],
                    $data['status'],
                    $data['date'],
                    $data['iata'],
                    $data['coordinates'],
                    $data['remarks']
                );
                $this->locodeRepository->save($locode,true);
                echo $x++;
            }catch (\Exception $e) {
                dd($e->getMessage(), $data1, $data);
            }


        }
    }



}