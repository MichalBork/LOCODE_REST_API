<?php

namespace App\Service;

use App\DTO\CodeFunctionDTO;
use App\Entity\CodeFunction;
use App\Repository\CodeFunctionRepository;
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
    private CodeFunctionRepository $codeFunctionRepository;
    private $file;
    private int $fileEnd = 0;


    public function __construct(LocodeRepository $locodeRepository, CodeFunctionRepository $codeFunctionRepository)
    {
        $this->locodeRepository = $locodeRepository;
        $this->codeFunctionRepository = $codeFunctionRepository;
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



    public function parseCsvFile(string $pathToFile, bool $update): void
    {
        $this->setFile(fopen($pathToFile, 'r'));
        do {
            $csv = $this->getNextPartOfFileAsArray(50);


            if ($update) {
                $this->updateLocode($csv);
            } else {
                foreach ($csv as $value) {
                    $this->fillDatabaseWithLocode($value);
                }
            }
        } while (!feof($this->file));
    }

    /**
     * @return array
     */
    public function getNextPartOfFileAsArray(int $partSize): array
    {
        $arr = [];
        while (($line = fgets($this->file)) !== false) {
            $partSize--;
            $arr[] = str_getcsv($line);
            if ($partSize === 0) {
                break;
            }
        }

        try {
            foreach ($arr as $key => $value) {
                if (isset($value[2])) {
                    $arr[$key] = array_merge([$value[0], $value[1] . $value[2]], array_slice($value, 3));
                } else {
                    unset($arr[$key]);
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage());

            dd($value);
        }

        return $arr;


    }


    private function setFile($file): void
    {
        if (!is_resource($file)) {
            throw new \InvalidArgumentException('File is not resource');
        }
        $this->file = $file;
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

                        $this->locodeRepository->save($locode,true );
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
            $data['status'],
            $data['date'],
            $data['iata'],
            $data['coordinates'],
            $data['remarks']
        );

        if (!empty($data['function'])) {
            $locode->addCodeFunction($this->parseFunctionColumn($data['function'], $locode));
        }


        $this->locodeRepository->save($locode, true);
    }


    public function parseFunctionColumn(string $function, Locode $locode): CodeFunctionDTO
    {
        $function = str_split($function);
        $stringToBool = function ($data) {
            return $data !== '-';
        };
        $codeFunctionDTO = CodeFunctionDTO::createFromArray(array_map($stringToBool, $function), $locode);

        if ($function[array_key_first($function)] === '0') {
            $codeFunctionDTO = CodeFunctionDTO::createCodeFunctionWithUnknown($locode);
        }


        return $codeFunctionDTO;
    }

    public function __destruct()
    {
        if (!is_resource($this->file)) {
            fclose($this->file);
        }
    }

}