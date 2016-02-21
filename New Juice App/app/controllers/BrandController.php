<?php

class BrandController extends BaseController {

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
		$brands = Brand::all();
		return View::make('brands.index', compact('brands'));
	}
	
	public function edit($id){
		$brand = Brand::find($id);
		return View::make('brands.edit', compact('brand'));
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
            return Redirect::to('brands/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $brand = Brand::find($id);
            $brand->name       = Input::get('name');
            $brand->save();

            // redirect
            Session::flash('message', 'Successfully updated brand!');
            return Redirect::to('brands');
        }
    }
	
	public function show() {
	  return View::make('brands.show');
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
            return Redirect::to('brands/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $brand = new Brand;
            $brand->name       = Input::get('name');
            $brand->save();

            // redirect
            Session::flash('message', 'Successfully created brand!');
            return Redirect::to('brands');
        }
    }
	
	public function create()
    {
        if(Auth::user()->group->id != 1){
            Session::flash('message', "Ah ah ah, you didn't say the magic word!");
            return Redirect::to('/');
        }
        // load the create form (app/views/nerds/create.blade.php)
        return View::make('brands.create');
    }
	
	public function delete($id){
		$brand = Brand::find($id);
		$brand->delete();
		
		return Redirect::action('BrandController@index');
	}
	
	public function destroy($id)
    {
        // delete
        $brand = Brand::find($id);
        $brand->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the brand!');
        return Redirect::to('brands');
    }
	
	
}