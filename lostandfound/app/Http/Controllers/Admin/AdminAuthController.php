<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    // Show admin login form
    public function create()
    {
        // Redirect if already logged in as admin
        if (Session::has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    // Handle admin login submission
    public function store(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Store admin session manually (separate from user auth guard)
        Session::put('admin_id', $admin->id);
        Session::put('admin_name', $admin->name);

        return redirect()->route('admin.dashboard');
    }

    // Handle admin logout
    public function destroy(Request $request)
    {
        Session::forget('admin_id');
        Session::forget('admin_name');

        return redirect()->route('admin.login');
    }
}