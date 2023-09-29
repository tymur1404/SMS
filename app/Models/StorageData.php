<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageData extends Model
{
    use HasFactory;

    protected $table = 'storage_data';
    protected $guarded = false;

}
