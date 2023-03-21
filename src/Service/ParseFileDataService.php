<?php

namespace App\Service;

use App\DTO\CodeFunctionDTO;
use App\Entity\Locode;

class ParseFileDataService
{

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
    const FILE_LINE_LIMIT = 1000;

    private $file;
    private UpdateDataService $updateDataService;

    public function __construct(UpdateDataService $updateDataService)
    {
        $this->updateDataService = $updateDataService;
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

    /**
     * @throws \InvalidArgumentException
     */
    public function setFile($file): void
    {
        if (!is_resource($file)) {
            throw new \InvalidArgumentException('File is not resource');
        }
        $this->file = $file;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function parseCsvFile(string $pathToFile, bool $update): void
    {
        try {
            $this->setFile(fopen($pathToFile, 'r'));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('File is not resource');
        }
        do {
            $csv = $this->getNextPartOfFileAsArray(self::FILE_LINE_LIMIT);


            if ($update) {
                $this->updateDataService->updateLocode($csv);
            } else {
                foreach ($csv as $value) {
                    $this->updateDataService->fillDatabaseWithLocode($value);
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

        foreach ($arr as $key => $value) {
            if (isset($value[2])) {
                $arr[$key] = array_merge([$value[0], $value[1] . $value[2]], array_slice($value, 3));
            } else {
                unset($arr[$key]);
            }
        }


        return $arr;
    }

    public function __destruct()
    {
        if (is_resource($this->file)) {
            fclose($this->file);
        }
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


}