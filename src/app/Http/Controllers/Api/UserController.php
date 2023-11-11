<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'last_name', 'first_name', 'is_admin', 'is_approved')
                    ->orderBy('updated_at', 'desc')
                    ->get();

        return $users;
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'is_admin' => ['required', 'boolean']
        ]);

        $user->is_admin = $validated['is_admin'];
        $user->save();
    }

    public function delete(User $user)
    {
        DB::beginTransaction();

        try {
            $user->towns()->detach();
            $user->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
