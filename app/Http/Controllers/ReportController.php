<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $role = $this->currentRole();
        return view('reports.index', compact('role'));
    }
}
