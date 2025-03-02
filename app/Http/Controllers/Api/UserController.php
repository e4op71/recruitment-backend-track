<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users)
            ->additional([
                'meta' => [
                    'total' => $users->count(),
                ],
            ]);
    }
}
