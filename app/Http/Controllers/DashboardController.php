<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class DashboardController extends Controller
{
    public function index()
    {
        $roleIds = auth()->user()->roles->pluck('id');
        $menus = Menu::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })->get();
        return view('layouts.dashboard', compact('menus'));
    }
}