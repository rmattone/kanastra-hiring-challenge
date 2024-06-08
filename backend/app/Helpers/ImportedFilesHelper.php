<?php

namespace App\Helpers;

use App\DataView\ImportedFilesDataView;
use App\Enums\ImportedFileEnum;
use App\Jobs\ProcessInvoicesJob;
use App\Services\ImportedFilesService;
use ZipArchive;

class ImportedFilesHelper
{

    protected $service;
    protected $dataView;

    public function __construct(
        ImportedFilesService $service,
        ImportedFilesDataView $dataView
    ) {
        $this->service = $service;
        $this->dataView = $dataView;
    }

    public function list()
    {
        $files = $this->service->get();
        return $files->map(fn ($file) => $this->dataView->view($file));
    }

    public function saveFile($request)
    {
        if (!$request->hasFile('csv_file')) {
            throw new \Exception('File not found');
        }

        $zipFile = $request->file('csv_file');
        $extractPath = storage_path('app/unzipped');
        $extractedFile = $this->extractZipFile($zipFile, $extractPath);        

        if (!file_exists($extractedFile)) {
            throw new \Exception('No file found after extraction');
        }
        
        $newFileName =  uniqid() . '-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $newFilePath = $extractPath . '/' . $newFileName;

        rename($extractedFile, $newFilePath);

        $importedFile = $this->service->store($request->input('name'), $request->input('description'), $newFileName, ImportedFileEnum::PENDING);

        return $importedFile;
    }
    
    private function extractZipFile($zipFile, $extractPath)
    {
        $zipFilePath = $zipFile->getPathname();
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath) !== TRUE) {
            throw new \Exception('Failed to unzip file');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        $expectedFileName = str_replace('.zip', '', $zipFile->getClientOriginalName());
        $extractedFile = $extractPath . '/' . $expectedFileName;

        return $extractedFile;
    }

    public function processFile($filePath)
    {
        dispatch(new ProcessInvoicesJob($filePath));
    }
}
