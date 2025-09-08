<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\PostInstagram;
use App\Models\PostFacebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificacaoController extends Controller
{
    private $data_atual;
    private $carbon;
    
    public function __construct()
    {
        $this->middleware('auth');        
        Session::put('url','coleta');
        $this->data_atual = session('data_atual');
    }

    public function index(Request $request)
    {
        
    }

    public function notificar()
    {
        $flag_enviar = false;
        $postagens = array();

        $postagens_instagram = array();
        $postagens_facebook = array();

        $postagens_instagram = PostInstagram::whereHas('clientes', function($q) {
                            $q->where('noticia_cliente.tipo_id', 6)
                              ->whereNull('noticia_cliente.deleted_at');
                            })
                ->orderBy('timestamp', 'desc')
                ->get();

        $postagens_facebook = PostFacebook::whereHas('clientes', function($q) {
                            $q->where('noticia_cliente.tipo_id', 5)
                              ->whereNull('noticia_cliente.deleted_at');
                            })
                ->orderBy('data_postagem', 'desc')
                ->get();

        foreach ($postagens_instagram as $key => $post) {
            
            $postagens[] = array('img' => 'instagram',
                                 'msg'  => $post->caption,
                                 'link' => $post->permalink);

            $flag_enviar = true;
        }

        foreach ($postagens_facebook as $key => $post) {
            
            $postagens[] = array('img' => 'facebook',
                                 'msg'  => $post->mensagem,
                                 'link' => $post->link);

            $flag_enviar = true;
        }

        $email = null;
        $msg = '';
        $data['msg'] = $msg;
        $data['postagens'] = $postagens;

        if($flag_enviar){

            $titulo = "Notificação de Monitoramento de Redes Sociais - ".date("d/m/Y H:i:s"); 
          
            //$mail_to = 'robsonferduda@gmail.com';

            $mail_to = 'alvaro@studioclipagem.com.br';

            Mail::send('notificacoes.redes-sociais.mensagem', $data, function($message) use ($mail_to, $msg, $titulo) {
                $message->to($mail_to)
                        ->subject($titulo);
                $message->from('boletins@clipagens.com.br','Studio Social');
            });
                       
        }
    }
}