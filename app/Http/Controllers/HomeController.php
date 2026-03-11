<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the splash screen / welcome page
     */
    public function index()
    {
        // If user is already authenticated, redirect to their dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        
        return view('welcome');
    }
}
