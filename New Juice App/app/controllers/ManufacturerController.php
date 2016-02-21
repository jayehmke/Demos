<?php

class ManufacturerController extends BaseController {

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
		$manufacturers = Manufacturer::all();
		return View::make('manufacturers.index', compact('manufacturers'));
	}
	
	public function edit($id){
		$Manufacturer = Manufacturer::find($id);
		return View::make('manufacturers.edit', compact('manufacturer'));
	}
	
	public function update($id)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('manufacturers/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $manufacturer = Manufacturer::find($id);
            $manufacturer->name       = Input::get('name');
            $Manufacturer->save();

            // redirect
            Session::flash('message', 'Successfully updated Manufacturer!');
            return Redirect::to('manufacturers');
        }
    }
	
	public function show() {
	  return View::make('manufacturers.show');
	}
	
	public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('manufacturers/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $manufacturer = new Manufacturer;
            $manufacturer->name       = Input::get('name');
            $manufacturer->save();

            // redirect
            Session::flash('message', 'Successfully created Manufacturer!');
            return Redirect::to('manufacturers');
        }
    }
	
	public function create()
    {
        if(Auth::user()->group->id != 1){
            Session::flash('message', "Ah ah ah, you didn't say the magic word!");
            return Redirect::to('/');
        }
        // load the create form (app/views/nerds/create.blade.php)
        return View::make('manufacturers.create');
    }
	
	public function delete($id){
		$Manufacturer = Manufacturer::find($id);
		$Manufacturer->delete();
		
		return Redirect::action('ManufacturerController@index');
	}
	
}