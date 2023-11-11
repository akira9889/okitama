<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AwaitingUserController extends Controller
{
    public function index()
    {
        $users = User::where('is_approved', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        return UserResource::collection($users);
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'is_admin' => ['required', 'boolean']
        ]);

        $user->is_admin = $validated['is_admin'];
        $user->is_approved = true;
        $user->save();
    }

    public function delete(User $user)
    {
        $user->delete();
    }
}
