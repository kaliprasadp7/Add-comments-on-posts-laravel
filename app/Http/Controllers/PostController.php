<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;

class PostController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request-> all(),[
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password'=> 'required|string|min:6|confirmed'
        ]);

        if($validator->fails())
        {
            return response()-> json($validator->errors(),400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message'=> 'User registerd successfully',
            'user' => $user
        ]);

    }

    //for login

    public function login(Request $request)
    {
        $validator = Validator::make($request-> all(),[
            'email' => 'required|string|email',
            'password'=> 'required|string|min:6'
        ]);

        if($validator->fails())
        {
            return response()-> json($validator->errors(),400);
        }

        if(!$token = auth()->attempt($validator->validated()))
        {
            return response()->json(['error'=>'Unauthorized']);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'User successfully logged out']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //for showing posts only
        // $result = Post::all();
        // return $result;

        // for showing posts and comments on this post using left join
        // $result =DB::table('posts')
        // ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        // ->get();


        // return response()->json($result);


        // $post = Post::where('id', $id)->first();
        // function lastComments()
        // {
        //     return $this->hasMany('Posts', 'post_id')->orderBy('created_at')->take(3);
        // }

        // Post::simplePaginate(15);

        // foreach($Post->lastComments as $comment){
        //     echo $comment;
        // }

        // @endforeach
            $n = 1;
        // $result = Post::all();
        // foreach($result as $value){
        //     echo $value;
        //     $comment=Comment::where('post_id' , $value->id )->get();
        //     foreach($comment as $value1){
        //         echo $value1;
        //     }
        // }

         $result = Post::all();
        foreach($result as $value){
            $data[$n]['post']= $value;
            $comment=Comment::where('post_id' , $value->id )->get();
            $m=1;
            foreach($comment as $value1){
                $data[$n]['message '.$m]= $value1;
                $m++;
            }
            $n++;
        }
        return response()->json($data);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new Post;
        $post->first_name = $request->first_name;
        $post->last_name = $request->last_name;
        $post->contact_num = $request->contact_num;
        $post->user_name = $request->user_name;
        $post->email = $request->email;
        $post->select_company = $request->select_company;
        $post->user_type = $request->user_type;
        $result = $post->save();
        if($result){
            return ["result"=>"Post added successfully"];
        }else{
            return ["result"=>"Post not added"];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $result = Post ::find($id);
        // return $result;

        $data['post'] = Post::find($id);
        $data['message'] = Comment::where('post_id' , $id )->get();
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $post->first_name = $request->first_name;
        $post->last_name = $request->last_name;
        $post->contact_num = $request->contact_num;
        $post->user_name = $request->user_name;
        $post->email = $request->email;
        $post->select_company = $request->select_company;
        $post->user_type = $request->user_type;
        $result = $post->save();
        if($result){
            return ["result"=>"Post updated successfully"];
        }else{
            return ["result"=>"Post not Updated"];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
