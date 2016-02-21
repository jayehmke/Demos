<?php

class UserController extends BaseController {

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
	

	public function index(){
		if(Auth::user()->group->id != 1){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('flavors');
		}
		$users = User::all();
		return View::make('users.index', compact('users'));
	}
	
	public function edit($id){


		if (Auth::user()->group->id != 1 && Auth::user()->id != $id){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('/');
		}

		$user   = User::find($id);
		$groups = Group::lists('name', 'id');
		
		$id = (int) $id;
		$hasBrands = DB::select(DB::raw('select * from brands
		left join brand_user 
		on brands.id = brand_user.brand_id
		and brand_user.user_id = ' . $id));
		
		return View::make('users.edit', compact('user', 'groups', 'hasBrands'));
	}
	
	public function update($id)
    {
	    $user = User::find($id);
	    
	    $brandList = Input::get('brand');
	    
		User::find($id)->brands()->detach();
		
		if($brandList){
			foreach ($brandList as $brand){
				$user->brands()->attach($brand);
			}
		}

	    
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'password'		=> 'alphaNum|min:8',
            'email'			=> 'required|email',
            'name'			=> 'required',
            'group_id'		=> 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('users/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $user = User::find($id);
            $user->name			= Input::get('name');
            $user->email		= Input::get('email');
            if (Input::get('password')){
				$user->password 	= Hash::make(Input::get('password'));
			}
			$user->group_id		= Input::get('group_id');
            $user->save();

            // redirect
            Session::flash('message', 'Successfully updated user!');
            return Redirect::to('users');
        }
    }
	
	public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'username'      => 'required|unique:users',
            'password'		=> 'required',
            'email'			=> 'required|email|unique:users',
            'name'			=> 'required',
            'group_id'		=> 'required'

        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('users/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $user = new User;
            $user->username		= Input::get('username');
            $user->name		    = Input::get('name');
            $user->email        = Input::get('email');
            $user->password     = Hash::make(Input::get('password'));
            $user->group_id		= Input::get('group_id');
            $user->save();

            // redirect
            Session::flash('message', 'Successfully created user!');
            return Redirect::to('users');
        }
    }
	
	public function create()
    {
		if(Auth::user()->group->id != 1){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('users');
		}
        $groups = Group::Lists('name', 'id');
        $brands = Brand::lists('name', 'id');
        return View::make('users.create', compact('groups', 'brands'));
    }
	
	public function delete($id){
		$user = User::find($id);
		$user->delete();
		
		return Redirect::action('UserController@index');
	}
	
	public function destroy($id)
    {
        // delete
        $user = User::find($id);
        $user->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the user!');
        return Redirect::to('users');
    }
	
	
}