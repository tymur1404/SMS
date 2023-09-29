<?php

namespace App\Repositories;

use App\Models\PostRoute;

class PostRouteRepository
{
    public static function getPostRoute(int $id): \Illuminate\Database\Eloquent\Model
    {
        return PostRoute::with('asteriskDongle')
            ->select('session_id', 'pid', 'server_pid', 'post_login')
            ->where('id', $id)
            ->first();
    }
}
