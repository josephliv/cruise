<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeadMails;

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
    public function index()
    {
        $leadMails = LeadMails::all();

        $view = \Auth::user()->is_admin ? view('dashboardadmin', compact('leadMails')) : view('dashboardagent', compact('leadMails')); ;
        return $view;
    }
}
