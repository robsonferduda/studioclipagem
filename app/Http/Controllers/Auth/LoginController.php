<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Client;
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

            //Neste bloco, realiza a lógica para gravar o id_cliente na sessão
            $u = User::find(Auth::user()->id);

            if($u->hasRole('administradores')){

                $id_cliente_padrao = Configs::where('key', 'cliente_padrao')->first()->value;
                $flag_regras = (Configs::where('key', 'flag_regras')->first()) ? Configs::where('key', 'flag_regras')->first()->value : 0;

                if(!Session::get('flag_regras') and $flag_regras){
                    ($flag_regras) ? Session::put('flag_regras', true) : Session::put('flag_regras', false);
                }

                if(!Session::get('cliente')){
                    $cliente = Client::find($id_cliente_padrao);
                    $cliente_session = array('id' => $cliente->id, 'nome' => $cliente->name);
                    Session::put('cliente', $cliente_session);
                }

            }else{
                
                $cliente = Client::where('id', Auth::user()->client_id)->first();

                $cliente_session = array('id' => $cliente->id, 'nome' => $cliente->name);
                Session::put('cliente', $cliente_session);

            }

            return redirect()->intended('home');
        }
  
        return redirect('login')
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Credenciais não encontradas",
                ]);
    }
}
