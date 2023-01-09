<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{   

  

    public function storeAvatar(Request $request){
        $request->validate([
            'avatar' => 'required|image|max:8000' //set conditions for the image
        ]);
        $user = auth()->user();  //reference the current user 
        
        $filename = $user->id . '-' . uniqid() . '.jpg'; //make the image name unique 

        //resize the user uploaded image and make it jpg
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $filename,$imgData); //store in the folder app public and storage folder
        
        //remove old avatar value from db, old avatar = current user -> avatar
        $oldAvatar = $user->avatar;

        //update the db with user moder > save
        $user->avatar = $filename;
        $user->save();

        //if not fallback avatar then use php function (end point and oldavatar value)
        if($oldAvatar != "/fallback-avatar.jpg"){
            Storage::delete(str_replace("/storage/","public/",$oldAvatar));
        }
        return back()->with('success', 'Avatar updated.');
    }

    public function showAvatarForm(){
        return view('avatar-form');
    }

    //SHARED data for profiles------------------------
    private function getSharedData($user){
        $isFollowing = 0;
        if(auth()->check()){
            $isFollowing = Follow::where([['user_id', '=', auth()->user()->id],['followeduser','=', $user->id]])->count();
        }

        View::share('sharedData',['isFollowing' => $isFollowing ,'avatar' => $user->avatar,'username' => $user->username, 'postCount'=>$user->posts()->count(), 'followerCount'=> $user->followers()->count(), 'followingCount'=> $user->followingTheseUsers()->count()]);
    }

    //PROFILE---------------------------------------
    //use model + matching url parameter to laravel to browse db
    public function profile(User $user){
        //$user is completely build user model
        //user is an instance of a user model > can look anything related user
        //created posts function > in user model now we can access to post model
      
        $this->getSharedData($user);
        return view('profile-post',['posts' => $user->posts()->latest()->get()]);
    }
    //Followers---------------------------------
    public function profileFollowers(User $user){
        $this->getSharedData($user);
       
        return view('profile-followers',['followers' => $user->followers()->latest()->get()]);
    }

    //Following-------------------------------------
    public function profileFollowing(User $user){    
        $this->getSharedData($user);   
       
        return view('profile-following',['following' => $user->followingTheseUsers()->latest()->get()]);
    }

    //LOGOUT-------------------------------------------------
    public function logout(){
        auth()->logout();
    return redirect('/')->with('success', 'You are now logged out.');
    }

    public function showCorrectHomepage(){
       if( auth()->check()){
            return view('homepage-feed',['posts' => auth()->user()->feedPosts()->latest()->paginate(4)]);
       }else{
            return view('homepage');
       }
    }

    //when post we are getting request data.. -> = call in
    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
            //attempt compares values user logged vs database
        if(auth()->attempt(['username'=>$incomingFields['loginusername'],'password'=>$incomingFields['loginpassword']])){
            $request->session()->regenerate();
            return redirect('/')->with('success','You have successfully logged in.');
        } else {
            return redirect('/')->with('failure', "Invalid login.");
        }
    }

    public function register(Request $request){
        //user controller catches the POST that contains following data
        //if these are left blank laravel doesnt post these
        //rule > laravel static class unique(users = table from db, username = from the table )
        $incomingFields =$request->validate([
            'username' => ['required', 'min:3','max:20',Rule::unique('users','username')],
            'email' => ['required', 'email', Rule::unique('users', "email")],
            'password' => ['required', 'min:4', 'confirmed' , Rule::unique('users', "password")]
        ]);
        //before passing the account creation lets hash our password
        $incomingFields['password'] = bcrypt($incomingFields['password']);

        //user is a model  from > app\models\user 
        //we can store the create user to the variable and pass it to the login
        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank for creating an account.');
    }
}
//migration > has to do with databases 
/* is how we create table, add tables and remove tables */
//model > CRUD operations > manage/define relationships (user relation to blogposts e.g. who owns the posts)