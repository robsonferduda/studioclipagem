<?php

namespace App\Http\Controllers;

use Mail;
use Carbon\Carbon;
use App\Models\Cliente;
use App\Models\NoticiaCliente;
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
        $clientes = Cliente::where('fl_instagram', true)->orWhere('fl_facebook', true)->get();

        foreach ($clientes as $key => $cliente) {

            $flag_enviar = false;
            $postagens = array();

            $postagens_instagram = array();
            $postagens_facebook = array();
            
            $postagens_instagram = PostInstagram::whereHas('clientes', function($q) use($cliente) {
                            $q->where('noticia_cliente.tipo_id', 6)
                                ->where('fl_enviada', false)
                                ->where('noticia_cliente.cliente_id', $cliente->id)
                                ->whereNull('noticia_cliente.deleted_at');
                            })
                ->orderBy('timestamp', 'desc')
                ->get();

            $postagens_facebook = PostFacebook::whereHas('clientes', function($q) use ($cliente) {
                                $q->where('noticia_cliente.tipo_id', 5)
                                    ->where('fl_enviada', false)
                                    ->where('noticia_cliente.cliente_id', $cliente->id)
                                    ->whereNull('noticia_cliente.deleted_at');
                                })
                    ->orderBy('data_postagem', 'desc')
                    ->get();

            foreach ($postagens_instagram as $key => $post) {

                $vinculo = NoticiaCliente::where('noticia_id', $post->id)
                                            ->where('tipo_id', 6)
                                            ->where('cliente_id', $cliente->id)
                                            ->first();

                //$vinculo->fl_enviada = true;
                //$vinculo->save();
                
                $postagens[] = array('img' => 'instagram',
                                     'msg'  => $post->caption,
                                     'link' => $post->permalink);

                $flag_enviar = true;
            }

            foreach ($postagens_facebook as $key => $post) {

                $vinculo = NoticiaCliente::where('noticia_id', $post->id)
                                            ->where('tipo_id', 5)
                                            ->where('cliente_id', $cliente->id)
                                            ->first();

                //$vinculo->fl_enviada = true;
                //$vinculo->save();
                
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
              
                $emails = array('robsonferduda@gmail.com','alvaro@studioclipagem.com.br');

                for ($i=0; $i < count($emails); $i++) { 

                    Mail::send('notificacoes.redes-sociais.mensagem', $data, function($message) use ($emails, $i, $msg, $titulo) {
                        $message->to($emails[$i])
                                ->subject($titulo);
                        $message->from('noreply@clipagem.online','Clipping de Redes Sociais');
                    });
                }                           
            }
        }
    }
}