<?php

namespace App;

class Utils
{
    public static function getHashtags($string, $lista_hashtags) {

        preg_match_all('/#(\w+)/',$string,$matches);
        for ($i=0; $i < count($matches[0]); $i++) { 
            $lista_hashtags[] = $matches[0][$i];
        }
        return $lista_hashtags;
    }

    public static function contaOrdenaLista($array)
    {
        $lista_frequencia = array_count_values($array);
        arsort($lista_frequencia);
        return $lista_frequencia;
    }

    public static function limpaCPF_CNPJ($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);

        return $valor;
    }

    public static function gerarHash(){

        //Isso irá gerar uma combinação de 8 caracteres, pseudo-randomicos.
        //Para passar para maiúsculo utilize o strtoupper, como strtoupper($resultado final). 
        //Para remover um dos caracteres, afim de torna-lo com 7 ao invés de 8, utilize o substr(), dessa forma substr($resultado_final, 1).

        return strtoupper(substr(bin2hex(random_bytes(5)), 2));

    }

    public static function gerar_senha($tamanho, $maiusculas, $minusculas, $numeros, $simbolos)
    {
        $senha = "";
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
      $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
      $nu = "0123456789"; // $nu contem os números
      $si = "!@#$%¨&*()_+="; // $si contem os símbolos
     
      if ($maiusculas) {
          // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável senha
          $senha .= str_shuffle($ma);
      }
     
        if ($minusculas) {
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável senha
            $senha .= str_shuffle($mi);
        }
     
        if ($numeros) {
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável senha
            $senha .= str_shuffle($nu);
        }
     
        if ($simbolos) {
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável senha
            $senha .= str_shuffle($si);
        }
     
        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável tamanho
        return substr(str_shuffle($senha), 0, $tamanho);
    }

    public static function get_post_action($name)
    {
        $params = func_get_args();

        foreach ($params as $name) {
            if (isset($_GET[$name])) {
                return $name;
            }
        }
    }

    public static function getDatabaseMessageByCode($errorCode)
    {
        switch ($errorCode) {
            
            case '23502':
                return '<i class="fa fa-times"></i> A restrição de valores não nulos foi violada';
                break;

            case '23505':
                return '<i class="fa fa-times"></i> Violação de restrição de valor único';
                break;

            case '22008':
                return '<i class="fa fa-times"></i> Formato de data inválido';
                break;
                            
            default:
                return '<i class="fa fa-times"></i> Código de erro desconhecido: '.$errorCode;
                break;
        }
    }
}