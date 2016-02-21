<?php

class IngredientController extends BaseController {

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
		$ingredients = Ingredient::orderBy('name', 'ASC')->get();

		return View::make('ingredients.index', compact('ingredients'));
	}
	
	public function edit($id){
		$ingredient = Ingredient::find($id);
		
		$manufacturers = Manufacturer::lists('name', 'id');

		
		return View::make('ingredients.edit', compact('ingredient', 'manufacturers'));
	}
	
	public function update($id)
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required',
            'current_level' => 'required|numeric',
            'par_level' => 'required|numeric',
            'alert_level' => 'required|numeric',
            'reorder_level' => 'required|numeric',
            'qty_on_order' => 'numeric',
            'comments' => '',
            'notes' => ''
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('ingredients/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $ingredient = Ingredient::find($id);
            $ingredient->name       = Input::get('name');
            $ingredient->current_level = Input::get('current_level');
            $ingredient->par_level = Input::get('par_level');
            $ingredient->alert_level = Input::get('alert_level');
            $ingredient->is_on_order = Input::get('is_on_order');
            $ingredient->reorder_level = Input::get('reorder_level');
            $ingredient->comments = Input::get('comments');
            $ingredient->notes = Input::get('notes');
            $ingredient->manufacturer_id = Input::get('manufacturer');
            $ingredient->save();

            // redirect
            Session::flash('message', 'Successfully updated ingredient!');
            return Redirect::to('ingredients');
        }
    }
	
	public function show() {
	  return View::make('ingredients.show');
	}
	
	public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required',
            'current_level' => 'required|numeric',
            'par_level' => 'required|numeric',
            'alert_level' => 'required|numeric',
            'reorder_level' => 'required|numeric',
            'qty_on_order' => 'numeric',
            'comments' => ''
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('ingredients/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $ingredient = new Ingredient;


            $ingredient->name       = Input::get('name');
            $ingredient->current_level = Input::get('current_level');
            $ingredient->par_level = Input::get('par_level');
            $ingredient->reorder_level = Input::get('reorder_level');
            $ingredient->alert_level = Input::get('alert_level');
            $ingredient->qty_on_order = Input::get('qty_on_order');
            $ingredient->comments = Input::get('comments');
            $ingredient->manufacturer_id = Input::get('manufacturer');
            $ingredient->save();

            // redirect
            Session::flash('error', 'Successfully created ingredient!');
            return Redirect::to('ingredients');
        }
    }
	
	public function create()
    {
		if(Auth::user()->group->id != 1){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('ingredients');
		}
        $manufacturers = Manufacturer::lists('name', 'id');
        return View::make('ingredients.create', compact('manufacturers'));
    }
	
	public function delete($id){
		$ingredient = Ingredient::find($id);
		$ingredient->delete();
		
		return Redirect::action('IngredientController@index');
	}
	
	public function destroy($id)
    {
        // delete
        $ingredient = Ingredient::find($id);
        $ingredient->flavors()->detach();
        $ingredient->delete();


        // redirect
        Session::flash('message', 'Successfully deleted the ingredient!');
        return Redirect::to('ingredients');
    }
    
    
    public function getcount($id){
	    
	    $ingredients = Ingredient::all();

		$ingArray = [];

	    foreach ($ingredients as $ingredient){

			$ingArray[$ingredient->id] = $ingredient->current_level;

		}

		$jsonArray = json_encode($ingArray);

	    //print_r($ingArray);

		//echo "<pre>";
		print_r($jsonArray);
		//echo "</pre>";

    }
    
    
    
    
    
    
	
	
}