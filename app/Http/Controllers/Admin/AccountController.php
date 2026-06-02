<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function students()
    {
        $students = User::where('role', 'student')
            ->latest()
            ->get();

        return view(
            'admin.accounts.students',
            compact('students')
        );
    }

    public function admins()
    {
        $admins = User::where('role', 'admin')
            ->latest()
            ->get();

        return view(
            'admin.accounts.admins',
            compact('admins')
        );
    }

    public function destroyStudent(User $user)
    {
        if ($user->role !== 'student') {
            return back();
        }

        $user->delete();

        return back()->with(
            'success',
            'Akun student berhasil dihapus'
        );
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nim' => 'required|unique:users,nim',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'password' => Hash::make(
                $request->password
            ),
            'role' => 'admin',
        ]);

        return back()->with(
            'success',
            'Admin berhasil ditambahkan'
        );
    }
}