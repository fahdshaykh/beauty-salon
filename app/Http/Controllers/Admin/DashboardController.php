<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;

class DashboardController extends Controller
{
    public function index()
    {
        $activeCompanies   = Company::where('is_active', 1)->count();
        $inactiveCompanies = Company::where('is_active', 0)->count();

        return view('admin.dashboard', compact('activeCompanies', 'inactiveCompanies'));
    }
}
