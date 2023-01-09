<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//end point + end point + controller function to do
//gate is similar to policies but policy is for crud operations but gate is just for simple operations "true or false"
Route::get('/admins-only', function(){
  return 'only admins can see this page';
})->middleware('can:visitAdminPages');

//user related routes-------------------------
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login');
Route::post('/register',[UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('mustBeLoggedIn');
Route::get('/manage-avatar', [UserController::class, "showAvatarForm"])->middleware('mustBeLoggedIn');
Route::post('/manage-avatar', [UserController::class, "storeAvatar"])->middleware('mustBeLoggedIn');


//Follow related routes--------------------------------
Route::post('/create-follow/{user:username}',[FollowController::class,'createFollow'])->middleware('mustBeLoggedIn');
Route::post('/remove-follow/{user:username}',[FollowController::class,'removeFollow'])->middleware('mustBeLoggedIn');

//blog post related routes---------------------
//here we just get the route and pass function and that function handles the logic
//route :: get + end point [controller::class + method] 
//post/{post} has to be the same value as in Post controller function parameter
Route::get('/create-post',[PostController::class,'showCreateForm'])->middleware('mustBeLoggedIn');//middleware will run 1st
Route::post('/create-post',[PostController::class,'storeNewPost'])->middleware('mustBeLoggedIn');
Route::get('/post/{post}', [PostController::class,'viewSinglePost']);
Route::delete('/post/{post}',[PostController::class, 'delete'])->middleware('can:delete,post');
Route::get('/post/{post}/edit', [PostController::class,'showEditForm'])->middleware('can:update,post');
Route::put('/post/{post}', [PostController::class, 'actuallyUpdate'])->middleware('can:update,post');
                                                                  //middleware can look into Postpolicy
//-----------------kernel handles the middleware commands
//Profile related routes--------------- //look via incoming username
Route::get('/profile/{user:username}', [UserController::class, 'profile']);
Route::get('/profile/{user:username}/followers', [UserController::class, 'profileFollowers']);
Route::get('/profile/{user:username}/following', [UserController::class, 'profileFollowing']);