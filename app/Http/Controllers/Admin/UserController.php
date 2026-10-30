<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'created_at'])
            ->with('roles')
            ->latest()
            ->get();

        return view('pages.admin.users.index', [
            'users' => $users,
            'totalUsers' => $users->count(),
            'activeUsers' => $users->filter(fn (User $user): bool => $user->email_verified_at !== null)->count(),
            'pendingUsers' => $users->filter(fn (User $user): bool => $user->email_verified_at === null)->count(),
        ]);
    }
}
