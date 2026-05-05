<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresensiMentah; // sama dengan AttendanceController

class PublicDashboardController extends Controller
{
    public function index()
    {
        $data = PresensiMentah::all(); // sama persis dengan AttendanceController
        return view('public.dashboard', compact('data'));
    }
}