<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginTokenEmail;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

    
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginRequest(Request $request)
    {
      $request->validate(['email' => 'required|string|email|exists:users']);

      $user = User::byEmail($request->email);

      $user->generateLoginToken();

      Mail::to($user)->queue(new LoginTokenEmail($user));

      return back()->withSuccess('Te hemos enviado un mensaje con el link para el login');
    }

    public function loginWithToken(Request $request)
    {
      $user = User::byEmail($request->email);

        if( Hash::check($user->login_token, $request->token))
        {
            Auth::login($user);

            $user->deleteLoginToken();

            return redirect('home')->withSuccess('Has iniciado sesion correctamente');
        }
        return redirect('home')->withDanger('El token es invalido, por favor solicitelo de nuevo');

    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        return redirect('/');
    }   
}
