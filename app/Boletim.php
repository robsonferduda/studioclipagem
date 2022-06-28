<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boletim extends Model
{
    const UPDATED_AT = null;
    
    protected $connection = 'mysql';
    protected $table = 'app_boletins';

    protected $fillable = ['status_envio'];
    
    /*
    SELECT 
                            web.id as id, 
                            web.id_cliente,
                            clientes.nome, 
                            area.titulo as area,
                            web.titulo as titulo, 
                            web.data_clipping as data, 
                            '' as segundos,
                            web.sinopse as sinopse, 
                            web.uf as uf, 
                            web.link as link, 
                            web.status as status, 
                            web.printurl as printurl, 
                            cidade.titulo as cidade_titulo, 
                            veiculo.titulo as INFO1,
                            parte.titulo as INFO2, 
                            ''  as INFOHORA,
                            CONCAT('web','') as clipagem,
                            area.ordem as ordem      
                        FROM app_web as web 
                        LEFT JOIN app_clientes as clientes ON web.id_cliente = clientes.id 
                        LEFT JOIN app_web_sites as veiculo ON veiculo.id = web.id_site
                        LEFT JOIN app_web_secao as parte ON parte.id = web.id_secao 
                        LEFT JOIN app_cidades as cidade ON cidade.id = web.id_cidade 
                        LEFT JOIN app_areasmodalidade as area ON (web.id_area = area.id)
                        WHERE data_clipping = '2022-06-26'
                        ORDER BY clientes.nome, titulo 
                        
SELECT 
                            web.id_cliente,
                            clientes.nome
                        FROM app_web as web 
                        LEFT JOIN app_clientes as clientes ON web.id_cliente = clientes.id 
                        LEFT JOIN app_web_sites as veiculo ON veiculo.id = web.id_site
                        LEFT JOIN app_web_secao as parte ON parte.id = web.id_secao 
                        LEFT JOIN app_cidades as cidade ON cidade.id = web.id_cidade 
                        LEFT JOIN app_areasmodalidade as area ON (web.id_area = area.id)
                        WHERE data_clipping = '2022-06-26'
                        GROUP BY web.id_cliente, clientes.nome 
                        ORDER BY clientes.nome */
                        
}