<?php

namespace App\Http\Controllers;

use App\Helpers\ImportedFilesHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Charges\StoreChargeRequest;
use App\Jobs\ProcessInvoicesJob;

class ChargeController extends Controller
{

    protected $importedFileHelper;

    public function __construct(ImportedFilesHelper $importedFileHelper)
    {
        $this->importedFileHelper = $importedFileHelper;
    }

    public function list()
    {
        try {
            $files = $this->importedFileHelper->list();
            return $this->success($files);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function store(StoreChargeRequest $request)
    {
        try {
            $file = $this->importedFileHelper->saveFile($request);
            // $this->importedFileHelper->processFile($file->path);
            // $this->importedFileHelper->processFile('app/unzipped/6663a0282cf51-2024-06-08-00-04-56.csv');
            dispatch(new ProcessInvoicesJob($file));
            // $file = true;
            return $this->success($file);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
