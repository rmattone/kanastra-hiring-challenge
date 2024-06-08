<?php

namespace App\Jobs;

use App\Enums\ImportedFileEnum;
use App\Models\BillingDocument;
use App\Models\ImportedFiles;
use App\Models\Invoices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProcessInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importedFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ImportedFiles $importedFile)
    {
        $this->filePath = $importedFile->path;
        $this->importedFile = $importedFile;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = storage_path($this->filePath);
        if (!file_exists($path)) {
            throw new \Exception("File not found: " . $this->filePath);
        }

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        // Itera sobre as linhas do CSV e insere no banco de dados
        $this->importedFile->status = ImportedFileEnum::PROCESSING;
        $this->importedFile->save();
        DB::beginTransaction();
        try {
            $id = $this->importedFile->id;
            foreach ($csv as $record) {
                Invoices::create([
                    'name' => $record['name'],
                    'governmentId' => $record['governmentId'],
                    'email' => $record['email'],
                    'debtAmount' => $record['debtAmount'],
                    'debtDueDate' => $record['debtDueDate'],
                    'debtId' => $record['debtId'],
                    'importedFileId' => $id,
                ]);
            }
            $this->importedFile->status = ImportedFileEnum::COMPLETED;
            $this->importedFile->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->importedFile->status = ImportedFileEnum::ERROR;
            $this->importedFile->save();
            throw $th;
        }
    }
}
