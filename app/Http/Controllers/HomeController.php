<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeadMails;
use Carbon\Carbon;

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

        
        if(\Auth::user()->is_admin){
            $leadMails = array(
                'subDay' => Carbon::now()->subDay(),
                'total' => LeadMails::count(),
                'available' => LeadMails::where('agent_id', '=', 0)->count(),
                'totalSent' => LeadMails::where('agent_id', '>', 0)->count(),
                'totalReject' => LeadMails::where('rejected', '=', 1)->count(),

                'total24h' => LeadMails::where('received_date', '>', Carbon::now()->subDay())->count(),
                'totalSent24h' => LeadMails::where('agent_id', '>', 0)->where('updated_at', '>', Carbon::now()->subDay())->count(),
                'totalReject24h' => LeadMails::where('agent_id', '=', -1)->where('updated_at', '>', Carbon::now()->subDay())->count(),
            );
        } else {
            $leadMails = array(
                'subDay' => Carbon::now()->subDay(),
                'total' => LeadMails::count(),
                'available' => LeadMails::where('agent_id', '=', 0)->count(),
                'totalSent' => LeadMails::where('agent_id', \Auth::user()->id)->count(),
                'totalReject' => LeadMails::where('agent_id', \Auth::user()->id)->where('rejected', '=', 1)->count(),

                'total24h' => LeadMails::where('agent_id', \Auth::user()->id)->where('updated_at', '>', Carbon::now()->subDay())->count(),
                'totalSent24h' => LeadMails::where('agent_id', \Auth::user()->id)->where('updated_at', '>', Carbon::now()->subDay())->count(),
                'totalReject24h' => LeadMails::where('agent_id', \Auth::user()->id)->where('rejected', '=', 1)->where('updated_at', '>', Carbon::now()->subDay())->count(),
            );            
        }

        $view = \Auth::user()->is_admin ? view('dashboardadmin', compact('leadMails')) : view('dashboardagent', compact('leadMails')); ;
        return $view;
    }
}
