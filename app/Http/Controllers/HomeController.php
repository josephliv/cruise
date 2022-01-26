<?php

namespace App\Http\Controllers;

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
        // Get an Agents Group Information
        $resultGroups = new \App\Group;
        $user = array(
            'group' => $resultGroups->group_name()
        );

        if (\Auth::user()->is_admin)
        {
            $leadMails = array(
                'subDay'      => Carbon::now()->startOfDay(),
                'total'       => LeadMails::count(),
                'available'   => LeadMails::where('agent_id', '=', 0)->count(),
                'totalSent'   => LeadMails::where('agent_id', '>', 0)->count(),
                'totalReject' => LeadMails::where('rejected', '=', 1)->count(),

                'total24h'       => LeadMails::where('received_date', '>', Carbon::now()->startOfDay())->count(),
                'totalSent24h'   => LeadMails::where('agent_id', '>', 0)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
                'totalReject24h' => LeadMails::where('rejected', '=', 1)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
            );
        } else
        {

            $leadMails = array(
                'subDay'      => Carbon::now()->startOfDay(),
                'total'       => LeadMails::count(),
                'available'   => LeadMails::where('agent_id', '=', 0)->count(),
                'totalSent'   => LeadMails::where('agent_id', \Auth::user()->id)->count(),
                'totalReject' => LeadMails::where('agent_id', \Auth::user()->id)->where('rejected', '=', 1)->count(),

                'total24h'       => LeadMails::where('agent_id', \Auth::user()->id)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
                'totalSent24h'   => LeadMails::where('agent_id', \Auth::user()->id)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
                'totalReject24h' => LeadMails::where('agent_id', \Auth::user()->id)->where('rejected', '=', 1)->where('updated_at', '>', Carbon::now()->startOfDay())->count(),
            );
        }

        $view = \Auth::user()->is_admin ? view('dashboardadmin', compact('leadMails')) : view('dashboardagent', compact('leadMails', 'user'));
        return $view;
    }
}
