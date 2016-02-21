<?php

class BottleController extends BaseController {

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
		$bottles = Bottle::all();
		return View::make('bottles.index', compact('bottles'));
	}
	
	public function edit($id){
		$bottle = Bottle::find($id);
		return View::make('bottles.edit', compact('bottle'));
	}
	
	public function update($id)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required',
            'case_size'      => 'required',
            'current_level' => 'required|numeric',
            'par_level' => 'required|numeric',
            'alert_level' => 'required|numeric'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('bottles/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $bottle = Bottle::find($id);
            $bottle->name       = Input::get('name');
            $bottle->case_size      = Input::get('case_size');
            $bottle->current_level = Input::get('current_level');
            $bottle->par_level = Input::get('par_level');
            $bottle->alert_level = Input::get('alert_level');
            $bottle->save();

            // redirect
            Session::flash('message', 'Successfully updated bottle!');
            return Redirect::to('bottles');
        }
    }
	
	public function show() {
	  return View::make('bottles.show');
	}
	
	public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required',
            'case_size'      => 'required|numeric',
            'current_level'      => 'required|numeric',
            'par_level' => 'required|numeric',
            'alert_level' => 'required|numeric'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('bottles/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $bottle = new Bottle;
            $bottle->name       = Input::get('name');
            $bottle->case_size      = Input::get('case_size');
            $bottle->current_level = Input::get('current_level');
            $bottle->par_level = Input::get('par_level');
            $bottle->alert_level = Input::get('alert_level');
            $bottle->save();

            // redirect
            Session::flash('message', 'Successfully created bottle!');
            return Redirect::to('bottles');
        }
    }
	
	public function create()
    {
        // load the create form (app/views/nerds/create.blade.php)
        return View::make('bottles.create');
    }
	
	public function delete($id){
		$bottle = Bottle::find($id);
		$bottle->delete();
		
		return Redirect::action('BottleController@index');
	}
	
	public function destroy($id)
    {
        // delete
        $bottle = Bottle::find($id);
        $bottle->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the bottle!');
        return Redirect::to('bottles');
    }
	
	
}