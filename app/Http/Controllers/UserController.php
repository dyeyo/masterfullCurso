<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function register(Request $request)
    {
        //RECOGER LOS DATOS DEL USUARIO POR POST
        $json=$request->input('json',null);
        //decodificar
        $parametros=json_decode($json);
        $parametrosArray=json_decode($json,true);
        // dd($parametros->name);
        //LIMPIAR LOS DATPOS POR ESPACIOS
        $parametrosArray=array_map('trim',$parametrosArray);

        if (!empty($parametros)&& !empty($parametrosArray)) {
            //VALIDAR DATOS
            $validate = \Validator::make($parametrosArray, [
                'name' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
                //validacion no ok
                $data = array(
                    'status' => 'error',
                    'code'=>404,
                    'message'=> 'Usuario fallido',
                    'errors'=>$validate->errors()
               );
            } else {
                //validacion ok

                //CIFRAR CONTRASEÑA
                $clave=hash('sha256',$parametros->password);

                //CREAR EL USUARIO
                $user=new User();
                $user->name=$parametrosArray['name'];
                $user->email=$parametrosArray['email'];
                $user->password=$clave;

                //dd($user);
                $user->save();
                $data = array(
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'El usuario se creo correctamente'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'datos no correctos',
            );
        }


        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth=new JwtAuth();

        //RECIBIR DATOS OR POST
        $json=$request->input('json',null);
        $parametros=json_decode($json);
        $parametros_array=json_decode($json,true);
        //VALIDAR DATOS
        $validate = \Validator::make($parametros_array, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            //validacion no ok
            $singup = array(
                'status' => 'error',
                'code'=>404,
                'message'=> 'Usuario no se identifico',
                'errors'=>$validate->errors()
            );
        } else {
            //CIFRAR CONTRASEÑA
            $pwd = hash('sha256', $parametros->password);
            //DEVOLVER TOKEN O DATOS
            $singup=$jwtAuth->singUp($parametros->email, $pwd);
            if (!empty($parametros->gettoken)){
                $singup=$jwtAuth->singUp($parametros->email, $pwd, true);
            }
        }


        return response()->json($singup,200);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $token=$request->header('Authorization');
        $jwtAuth=new JwtAuth();
        $checkToken=$jwtAuth->checkToken($token);
        if ($checkToken){
            echo "<h1>LOGIN CORRECTO</h1>";
        }else{
            echo "<h1>LOGIN INCORRECTO</h1>";
        }
    }

    public function destroy($id)
    {
        //
    }
}
