<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('giang_vien')) {
            return redirect()->route('gv.dashboard');
        }

        if ($user->hasRole('hoc_vien')) {
            return redirect()->route('hv.dashboard');
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Tài khoản không có vai trò hợp lệ.');
    }
}
