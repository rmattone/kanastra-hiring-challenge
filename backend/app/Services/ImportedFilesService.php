<?php

namespace App\Services;

use App\Enums\ImportedFileEnum;
use App\Models\ImportedFiles;

class ImportedFilesService
{
    public static function get()
    {
        $query = ImportedFiles::query()
            ->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function store(string $name, string $description, string $newFileName, ImportedFileEnum $status = ImportedFileEnum::PENDING)
    {
        return ImportedFiles::create([
            'name' => $name,
            'description' => $description,
            'path' => 'app/unzipped/' . $newFileName,
            'status' => $status
        ]);
    }
}
