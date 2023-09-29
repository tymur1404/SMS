<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRoute extends Model
{
    use HasFactory;

    protected $table = 'post_routes';
    protected $guarded = false;

    public function asteriskDongle()
    {
        return $this->hasOne(AsteriskDongle::class, 'ppid', 'id');
    }
}
