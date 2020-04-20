<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use ReallySimpleJWT\Token;

class UserController extends Controller
{
    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $user = DB::table('Users')->where('email', '=', $email)->get();
        if(sizeof($user) == 0 || !Hash::check($password, $user[0]->password)) {
            abort(403);
        }else{
            $payload = [
                'iat' => time(),
                'uid' => 1,
                'iss' => 'ReactyNews'
            ];
            $token = Token::customPayload($payload, env('KEY'));
            return $token.';'.$user[0]->name.';'.$user[0]->country.';'.$user[0]->id;
        }
    }
     public function signup(Request $request){
        if(!Token::validate($request->header('Authorization'), env('KEY'))){
            return abort(404);
        }
        $user = DB::table('Users')->where('email', '=', $request->input('email'))->get();
        if(sizeof($user) > 0){
            return abort(400);
        }
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('name');
        $country = $request->input('country');
        $hashed = Hash::make($password, [
            'rounds' => 12
        ]);
        $id = DB::table('Users')->insertGetId([
            'email' => $email,
            'name' => $name,
            'country' => $country,
            'password'=>$hashed
        ]);
        $payload = [
            'iat' => time(),
            'uid' => 1,
            'iss' => 'ReactyNews'
        ];
        $token = Token::customPayload($payload, env('KEY'));
        return $token.';'.$name.';'.$country.';'.$id;
    }
}
