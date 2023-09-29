<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateLinkToCompany extends Model
{
    use HasFactory;

    protected $table = 'gate_link_to_company';
    protected $guarded = false;

}
