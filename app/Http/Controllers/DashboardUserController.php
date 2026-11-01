<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardUserController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.public.dashboard.index');
    }
}
