<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class HomeController extends Controller
{

    public function index(){
        $students = Student::latest()->get();
        return view('auth.login',compact('students'));
    }

    public function login(){
        return view('auth.login');
    }
    
    public function register(){
        return view('auth.register');
    }

    public function redirect()
    {
        return view('admin.dashboard');
    }
}
