<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//this returns object of the users from db > point of view of the follower
class Follow extends Model
{
    use HasFactory;

    public function userDoingTheFollowing(){
        return $this->belongsTo(User::class, 'user_id'); //from db table
    }
    public function userBeingFollowed(){
        return $this->belongsTo(User::class, 'followedUser'); //from db table
    }
}
//these gives you objects such as name isntead of id
