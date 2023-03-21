<?php

namespace App\Service;

class DownloadFilesService
{

    const FILE_NAME = 'lastUpdateDate.txt';
    const FILE_PATH = __DIR__ . '/../../' . self::FILE_NAME;
    private UpdateDataService $updateDataService;

    public function __construct(UpdateDataService $updateDataService)
    {
        $this->updateDataService = $updateDataService;
    }

    public function saveLastUpdateDate(string $lastUpdateDate): void
    {
        file_exists(self::FILE_PATH) ? unlink(self::FILE_PATH) : null;
        file_put_contents(self::FILE_PATH, $lastUpdateDate);
    }

    public function unzipFile(string $file, string $destination): void
    {
        $zip = new \ZipArchive();
        $zip->open($file);
        $zip->extractTo($destination);
        $zip->close();
        unlink($file);
    }

    public function checkIfUpdateIsNeeded(string $updateDate): bool
    {
        if (!file_exists(self::FILE_PATH)) {
            $this->createFileWithDate($updateDate);
            return true;
        }
        return !$this->checkLastUpdateDate($updateDate);
    }


    public function checkLastUpdateDate(string $updateDate): bool
    {
        file_get_contents(self::FILE_PATH);
        return $updateDate === file_get_contents(self::FILE_PATH);
    }

    public function createFileWithDate(string $lastUpdateDate): void
    {
        $this->saveLastUpdateDate($lastUpdateDate);
    }
}