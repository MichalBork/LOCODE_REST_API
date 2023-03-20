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


    public function parseCsvFile(string $pathToFile): void
    {
        $csv = $this->getArr($pathToFile);
        $databaseData = $this->locodeRepository->findOneBy(['name' => '.ANDORRA']);
        if (!empty($databaseData)) {
            $this->updateLocode($csv);
        } else {
            foreach ($csv as $value) {
                $this->fillDatabaseWithLocode($value);
            }
        }
    }

    /**
     * @param string $pathToFile
     * @return array
     */
    public function getArr(string $pathToFile): array
    {
        $arr = array_map('str_getcsv', file($pathToFile));
        foreach ($arr as $item => $value) {
            if (!isset($value[1])) {
                unset($arr[$item]);
                continue;
            }
            $arr[$item] = array_merge([$value[0], $value[1] . $value[2]], array_slice($value, 3));
        }
        return $arr;
    }

    private function updateLocode(array $csv): void
    {
        foreach ($csv as $value) {
            $data = $this->createArrayWithKeyAndCorrectEncoding($value);
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
        $data = $this->createArrayWithKeyAndCorrectEncoding($value);

        $this->newLocode($data);
    }

    /**
     * @param mixed $value
     * @return array|false[]|string[]
     */
    public
    function createArrayWithKeyAndCorrectEncoding(
        mixed $value
    ): array {
        $encode = function ($data) {
            return iconv('windows-1250', "UTF-8", $data);
        };
        $data1 = array_combine(self::COLUMN_NAMES, $value);
        $data = array_map($encode, $data1);
        return $data;
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
            $data['function'],
            $data['status'],
            $data['date'],
            $data['iata'],
            $data['coordinates'],
            $data['remarks']
        );
        $this->locodeRepository->save($locode, true);
    }
}