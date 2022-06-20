<?php

namespace App\Http\Controllers;

use App\Collect;
use App\NotificationLog;
use App\Classes\IGHashTag;
use App\Classes\IGMention;
use App\Twitter\TwitterCollect;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\EmailRequest;
use Illuminate\Support\Facades\Session;

class ColetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Session::put('url','coletas');
    }

    public function index()
    {
        $coletas = Collect::with('client')->orderBy('created_at','DESC')->paginate(10);
        $notificacoes = NotificationLog::with('client')->orderBy('created_at','DESC')->paginate(10);

        return view('coleta/index', compact('coletas','notificacoes'));
    }

    public function twitter()
    {
        (new TwitterCollect())->pullMedias();
        Flash::success("As coletas do Twitter foram realizadas com sucesso");

        return redirect('coletas')->withInput(); 
    }

    public function instagram()
    {
        (new IGHashTag())->pullMedias();
        (new IGMention())->pullMedias();
        Flash::success("As coletas do Instagram foram realizadas com sucesso");

        return redirect('coletas')->withInput(); 
    }

    public function facebook()
    {
        (new TwitterCollect())->pullMedias();
        Flash::success("As coletas do Perfil do Facebook foram realizadas com sucesso");

        return redirect('coletas')->withInput(); 
    }

    public function facebookPage()
    {
        (new TwitterCollect())->pullMedias();
        Flash::success("As coletas das Páginas do Facebook foram realizadas com sucesso");

        return redirect('coletas')->withInput(); 
    }
}