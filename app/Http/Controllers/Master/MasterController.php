<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

class MasterController extends Controller
{
    public function home()
    {
        return view('master.home');
    }

    public function clinicas()
    {
        return view('master.clinicas');
    }

}
