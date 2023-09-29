<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingGate extends Model
{
    use HasFactory;

    protected $table = 'working_gate';
    protected $guarded = false;

}
