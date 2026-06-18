<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('pages.dashboard', [
            'totalPatients' => Patient::count(),
            'totalRecords' => MedicalRecord::count(),
            'recentPatients' => Patient::latest()->take(5)->get(),
        ]);
    }
}
