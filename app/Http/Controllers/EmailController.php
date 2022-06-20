<?php

namespace App\Http\Controllers;

use App\Email;
use App\Client;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\EmailRequest;
use Illuminate\Support\Facades\Session;

class EmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function atualizarSituacao($email_id)
    {
        $email = Email::find($email_id);
        $email->status = !$email->status;
        $email->save();
        
        return redirect('client/emails/'.$email->client->id);
    }

    public function store(EmailRequest $request)
    {
        if(Email::create($request->all())){
            Flash::success('<i class="fa fa-check"></i> Cadastro realizado com sucesso');
        }else{
            Flash::info('<i class="fa fa-info"></i> Erro ao cadastrar email');
        }
        return redirect('client/emails/'.$request->client_id);
    }

    public function destroy($id)
    {
        $email = Email::find($id);
 
        if($email->delete())
            Flash::success('<i class="fa fa-check"></i> Email <strong>'.$email->ds_email.'</strong> excluído com sucesso');
        else
            Flash::error("Erro ao excluir email");

        return redirect('client/emails/'.$email->client->id);
    }
}