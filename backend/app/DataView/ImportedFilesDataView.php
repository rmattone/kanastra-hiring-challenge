<?php

namespace App\DataView;

use App\Models\ImportedFiles;
use Carbon\Carbon;

class ImportedFilesDataView
{

    public function view(ImportedFiles $file)
    {
        return [
            'id' => $file->id,
            'name' => $file->name,
            'description' => $file->description,
            'status' => $file->status ?? 1,
            'path' => explode('/',$file->path)[2],
            'created_at' => Carbon::parse($file->created_at)->format('d/m/Y H:i:s')
        ];
    }
}
