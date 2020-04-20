<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ReallySimpleJWT\Token;

class ArticlesController extends Controller
{
    public function getAll($country){
        $response = [];
        $url = "https://newsapi.org/v2/top-headlines?country=".$country."&apiKey=39d084dff8004ff5bb908b1feaba34de";
        $data = json_decode(file_get_contents($url), true);
        $articles = $data["articles"];
        foreach($articles as $article){
            $temp = (object)[];
            $temp->image = $article["urlToImage"];
            $temp->url = $article["url"];
            $temp->title = $article["title"];
            $temp->description = $article["description"];
            array_push($response, $temp);
        }
        header('Content-type: application/json');
        return response()
            ->json($response);
    }

    public function save(Request $request){
        if(!Token::validate($request->header('Authorization'), env('KEY'))){
            return abort(404);
        }
        DB::table('Saved')->insert([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'url' => $request->input('url'),
            'image' => $request->input('image'),
            'user_id' => $request->input('id')
        ]);
        return;
    }

    public function getSaved(Request $request, $id){
        if(!Token::validate($request->header('Authorization'), env('KEY'))){
            return abort(404);
        }
        $articles = DB::table('Saved')->where('user_id','=', $id)->get();
        header('Content-type: application/json');
        return response()
            ->json($articles);
    }

    public function delete(Request $request){
        DB::delete('delete from Saved where id = '.$request->input("id").' AND user_id = '.$request->input("user_id").';');
        return;
    }

}
