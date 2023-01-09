<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    //this needed so we can post on db
    protected $fillable = ['title', 'body', 'user_id'];

    //this value will be returned in a single post $post -> func name -> username
    public function user(){
        //this looks who own the blog "this-blog = blog post belongs to 
        //User from the model and the relation user_id as a foreign key   
        return $this->belongsTo(User::class,'user_id');
    } // $incomingFields['user_id'] = auth()->id(); done in postcontroller
}
//User comes from the model User

//create post migration has the foreign key user_id