<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Models\Cliente;
use App\Configs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the redirect path based on user role
     */
    protected function redirectTo()
    {
        if (Auth::user()->hasRole('cliente')) {
            return '/cliente/dashboard';
        }
        
        return RouteServiceProvider::HOME;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {

            if(Auth::user()->hasRole('cliente')){
                Session::put('cliente', Cliente::where('id', Auth::user()->client_id)->first());
                Session::put('data_atual', date('Y-m-d'));
                
                // Redireciona cliente diretamente para o dashboard
                return redirect('cliente/dashboard');
            }

            Session::put('data_atual', date('Y-m-d'));
        
            // Para outros tipos de usuário, mantém o redirecionamento padrão
            return redirect()->intended('home');
        }
  
        return redirect('login')
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Credenciais não encontradas",
                ]);
    }
}
