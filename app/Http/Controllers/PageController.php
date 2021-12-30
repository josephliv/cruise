<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;

class PageController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @param $page
     * @return Application|void
     */
    public function index($page) {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }
        return abort(404);
    }
}
