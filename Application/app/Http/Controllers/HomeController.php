<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }

    public function about()
    {
        return view('about');
    }

    public function engineering()
    {
        return view('services.engineering');
    }

    public function construction()
    {
        return view('services.construction');
    }

    public function maintenance()
    {
        return view('services.maintenance');
    }

    public function certifications()
    {
        return view('services.certifications');
    }

    public function gallery()
    {
        return view('gallery');
    }
}
