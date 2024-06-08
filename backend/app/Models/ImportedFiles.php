<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportedFiles extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'imported_files';
    protected $primaryKey = 'id';
    protected $fillable =  [
        'name',
        'description',
        'path',
        'status'
    ];
}
