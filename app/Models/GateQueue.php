<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateQueue extends Model
{
    use HasFactory;

	protected $table = 'gate_queue';
    protected $guarded = false;

}
