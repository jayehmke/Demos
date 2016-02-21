<?php

class SessionController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	
	
	public function store()
    {
		$data = Input::all();
		
		$attempt = Auth::attempt([
			'username' => $data['username'],
			'password' => $data['password']
		]);
		
		if ($attempt){
			return Redirect::intended('/flavors')->with('flash_message', 'You have been logged in!');
		}
		
		else{
			Session::flash('message', 'Username/Password Incorrect. Please Try Again.');
			return Redirect::to('/login')->withInput();
		}
		
		
    }
	
	public function create()
    {
	    if (Auth::check())
			{
			    return Redirect::to('flavors');
			}
		return View::make('sessions.create');
    }
	
	
	public function destroy()
    {
		Auth::logout();
		return Redirect::to('login');
    }
	
	
}