<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login_v2');
    }

    /**
     * Where to redirect users after login (fallback).
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Handle post-authentication redirect based on user role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */


    protected function authenticated($request, $user)
{
    if ($user->hasRole('administrator')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('staff')) {
        return redirect()->route('staff.dashboard');
    }

    if ($user->hasRole('implementing_agency') || $user->hasRole('agency')) {
        return redirect()->route('agency.dashboard');
    }

    if ($user->hasRole('division_head') || $user->hasRole('chief')) {
        return redirect()->route('chief.dashboard');
    }

    if ($user->hasRole('pdipbd_staff')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect('/home'); // fallback
}

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

   
}
