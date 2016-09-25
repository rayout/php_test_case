<?php

use Illuminate\Http\Request;
use App\User as User;
use App\Message as Message;
use Illuminate\Support\Facades\Hash;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::post('/create-user', function (Request $request) {

	$email = $request->input('email');
	$name = $request->input('name');
	$password = Hash::make($request->input('password'));

	$user = User::create([
		'name' => $name,
		'email' => $email,
		'password' => $password
	]);

    return $user;

});


Route::post('/login-user', 'AuthenticateController@authenticate');

Route::get('/get-auth-user', 'AuthenticateController@getAuthenticatedUser')->middleware('jwt.auth');

Route::get('/get-user/{id}', function(Request $request, $id){

	return User::find($id);

})->middleware('jwt.auth');


Route::get('/get-user-messages/{id}', function(Request $request, $id){

	return Message::where('user_id',$id)
	->join('users', 'messages.user_id', '=','users.id')
	->orderBy('mes_id', 'desc')
	->get();

});



Route::get('/all-messages/{paginate_count}', function (Request $request, $paginate_count) {


	return Message::join('users', 'messages.user_id', '=','users.id')
	->orderBy('mes_id', 'desc')
	->paginate($paginate_count);

})->middleware('cors');


Route::post('/write-message', function(Request $request) {

	$message = Message::create($request->all());
	return $message;

})->middleware('jwt.auth');




Route::get('/get-user-messages', function (Request $request) {

    return User::messages()->all();

})->middleware('jwt.auth');




Route::any('{all}', function () {
    return view('index');
})->where(['all' => '.*']);