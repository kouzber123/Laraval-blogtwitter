<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //type hinting of post model and incoming request 
    public function actuallyUpdate(Post $post, Request $request){
        $post['body'] = Str::markdown($post->body);
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);  

        //update command
        $post->update($incomingFields);
        return back()->with('success', 'Post successfully updated.');
        //things powered by models have direct connection to database
    }

    public function showEditForm(Post $post){
        return view('edit-post',['post' => $post]);
    }
   
    public function delete(Post $post){
        $post->delete();
        return redirect('profile/'. auth()->user()->username)->with('success', 'Post succesfully deleted.');
    }

    //show new blade template for single posts need id, power of model & type hinting = matching endpoint
    //laravel can look up from the database with this Post $value
    //pass the values into the endpoint file
    public function viewSinglePost(Post $post){



        // return $post -> user_id; & adding markdown functionalities
        $post['body'] = Str::markdown($post->body);
        return view('single-post',['post' => $post]);
    }//post method also will get post.php
//returning file and passing post parameter that contains model : post.php
    

public function storeNewPost(Request $request){
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        
        //before we save to db = strip any malicious tags
        //similar to hashing
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id(); //auth().id() user_id is the current user 
        //store the post into variable and also create a new Post into database 
        //with post model
       $newPost = Post::create($incomingFields);
        return redirect("/post/{$newPost->id}")->with('success','New Post successfully created.');
    }


    public function showCreateForm(){       
        return view("create-post");
    }
}

//Post::create = Models folder 


  //  //policy way to delete
        // if(auth()->user()->cannot('delete', $post)){
        //     return 'You cannot do that.';
        // }