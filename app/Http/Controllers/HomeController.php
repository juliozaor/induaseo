
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->rol == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->rol == 'supervisor') {
            return redirect()->route('supervisor.dashboard');
        } else {
            return redirect()->route('user.dashboard');
        }
    }
}