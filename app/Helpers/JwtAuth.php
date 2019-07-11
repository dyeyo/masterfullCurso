<?php
/**
 * Created by PhpStorm.
 * User: Dyego
 * Date: 29/04/2019
 * Time: 10:21 PM
 */

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;


class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key='ESTA_ES_LA_CLAVE';
    }

    public function singUp($email, $password, $getToken=null){
        //BUSCAR EL USUARIO CON SUS CREDECIALES
        $user=User::where([
            'email'=>$email,
            'password'=>$password,
        ])->first();

        //COMPRABAR SI SON CORRECTAS(objeto)

        $singup=false;
        if (is_object($user )){
            $singup=true;
        }

        //GENERAR TOKEN CON DATOS DEL USUARIO IDENTIFICADO

        if ($singup){
            $token=array(
                //id
                'sub'=> $user->id,
                'email'=>$user->email,
                'name'=>$user->name,
                'surname'=>$user->surname,
                //tiempo del token
                'iat'=>time(),
                //tiempo de expedicion a la semana
                'exp'=>time()+(7*24*60*60)

            );
                             //token.key,algoritomo de decodificar
            $jwt = JWT::encode($token,$this->key,'HS256');
            $decode=JWT::decode($jwt,$this->key,['HS256']);

            //DEVOLVER LOS DATOS DECODIFICADOS O EL TOKEN, EN FUNCION DE UN PARAMETRO
            if (is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decode;
            }

        }else{
            $dta=array(
                'status'=>'error',
                'message'=>'login incorrecto'
            );

        }
        return $data;
    }

    public function checkToken($jwt,$getIdentity=false){
        $auth= false;
        //decodificacion del token
        try{
            $jwt=str_replace('"','',$jwt);
            $decoded=JWT::decode($jwt,$this->key,['HS256']);
        }catch (\UnexpectedValueException $e){
            $auth=false;
        }catch (\DomainException $e){
            $auth=false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth=true;
        }else{
            $auth=false;
        }

        //FLAG DEL IDENTITY
        if ($getIdentity){
            return $decoded;
        }

        return $auth;
    }

}