<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Sp3;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /** home dashboard */
    public function index()
    {
        $sp3s = Sp3::whereYear('tgl_sp3', date('Y'))->get();
        $billings = Billing::whereYear('tanggal_masuk', date('Y'))->get();
        $bulan = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $dataSp3 = $sp3s->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl_sp3)->format('F');
        })->map->count();
        $dataBilling = $billings->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal_masuk)->format('F');
        })->map->count();
        $nilaiSp3 = collect($bulan)->map(fn($b) => $dataSp3[$b] ?? 0)->values();
        $nilaiBilling = collect($bulan)->map(fn($b) => $dataBilling[$b] ?? 0)->values();
        return view('dashboard.home', compact('bulan', 'sp3s', 'billings', 'nilaiSp3', 'nilaiBilling'));
    }
    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        return view('dashboard.teacher_dashboard');
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        return view('dashboard.student_dashboard');
    }
}
