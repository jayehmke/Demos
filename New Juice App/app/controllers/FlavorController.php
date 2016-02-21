<?php

class FlavorController extends BaseController {

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
		/*
$flavors = DB::table('flavors')
					->join('brands', 'brands.id', '=', 'flavors.brand_id')
					->join('brand_user', 'brand_user.brand_id', '=', 'flavors.brand_id')
					->where('brand_user.user_id', '=', '19')
					->get();
*/

		$char = isset($_GET['flava']) ? $_GET['flava'] : "a";

		$userBrands = User::find(Auth::user()->id)->brands->toArray();

		$brands = array();
		foreach ($userBrands as $userBrand){
			array_push($brands, $userBrand['id']);
		}

		if (!$brands){
			Auth::logout();
			Session::flash('flash_error', "You do not have any juice brands assigned. Please have an administrator assign brands to your account before you can login.");
			return Redirect::to('login');
		}
		
		$flavors = Flavor::whereIn('brand_id', $brands)->where('title', 'like', $char.'%')->get()->sortBy('title');

		$allFlavors = Flavor::whereIn('brand_id', $brands)->get()->sortBy('title');

		$flavorsearch = array();
		foreach ($allFlavors as $flavor):
			$flavorletter = $flavor->title;
			$flavorletter = strtoupper($flavorletter[0]);
			array_push($flavorsearch, $flavorletter);
		endforeach;
		$flavorlist =  array_unique($flavorsearch);
		sort($flavorlist);
		$flavorCount = count($flavorlist);
		$flavorHalfOne = array_slice($flavorlist, 0, $flavorCount / 2);
		$flavorHalfTwo = array_slice($flavorlist, $flavorCount / 2);
				
		return View::make('flavors.index', compact('flavors', 'flavorHalfOne', 'flavorHalfTwo'));
	}
	
	public function edit($id){

		if(Auth::user()->group->id != 1){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('/');
		}

		$flavor = Flavor::find($id);

		$ingredients = Ingredient::orderBy('name')->lists('name', 'id');
		
		//$ingredients = DB::table('ingredients')->lists('name', 'id');
		
		$brands = Brand::lists('name', 'id');
		
		return View::make('flavors.edit', compact('flavor', 'ingredients', 'brands'));
	}
	
	public function update($id)
    {
	    
	    $flavor = Flavor::find($id);

	    $ingredientsList = Input::get('ingredient');
	    
		$amounts = Input::get('amount');
		
		$addIngredients = Input::get('ingredient_add');
		
		$deleteIng = Input::get('deleteIng');

		Flavor::find($id)->ingredients()->detach();
	    
	    if ($addIngredients){
		    $flavor->ingredients()->attach($addIngredients);
	    }
	    
		$i = 0;
		foreach ($ingredientsList as $ingredient){
			
			$flavor->ingredients()->attach($ingredient, array('amount' => number_format((float)$amounts[$i], 2, '.', '')));	
			$i++;
		}
		
		if($deleteIng){
			Flavor::find($id)->ingredients()->detach($deleteIng);
		}
	    
	    
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'title'       => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('flavors/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $flavor = Flavor::find($id);
            $flavor->title      = Input::get('title');
            $flavor->body		= Input::get('body');
            $flavor->brand_id	= Input::get('brand');
            $flavor->save();

            // redirect
            Session::flash('message', 'Successfully updated flavor!');
            return Redirect::to('flavors/' . $flavor->id . '/edit');
        }
    }
    
    public function make($id, $qty){

		$status = 0;
	    
	    $ingredients = Flavor::find($id)->ingredients;

		foreach ($ingredients as $ingredient){

			$updateIng = Ingredient::find($ingredient->id);

			$currentLevel = $updateIng->current_level;

			$using = $ingredient->pivot->amount * $qty;

			if ($currentLevel < $using){
				$status = 0;
				return $status;
			}
			else{
				$status = 1;
			}

		}

	    foreach ($ingredients as $ingredient){
		    $updateIng = Ingredient::find($ingredient->id);

				$updateIng->current_level = ($updateIng->current_level - $ingredient->pivot->amount * $qty);
				$updateIng->save();

//			Mail::send('emails.alert', array('key' => 'value'), function($message)
//			{
//				$message->to('jason.ehmke@madvapes.com', 'John Smith')->subject('Welcome!');
//			});


			if ($updateIng->current_level <= $updateIng->alert_level && $updateIng->is_on_order == 0){




				Mail::send('emails.alert', array(), function($message) use($updateIng)
				{
					$message->to('paul.verlie@madvapes.com');
					$message->to('scottc@madvapes.com');
					$message->subject($updateIng->name . ' Is At or Below Alert Level!');
					$message->ingredient = $updateIng->name;
					$message->current_level = $updateIng->current_level;

				});
			}

	    }

		return $status;

    }
	
	public function show() {
	  return View::make('flavors.show');
	}
	
	public function store()
    {
        // validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'title'       => 'required',
            'body' => '',
            'ingredient_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('flavors/create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            
            $ingredients = Input::get('ingredient_id');

            $flavor = new Flavor;
            $flavor->title		= Input::get('title');
            $flavor->body		= Input::get('body');
            $flavor->brand_id	= Input::get('brand');
            $flavor->save();
            
			$flavor->ingredients()->attach($ingredients);

            // redirect
            Session::flash('message', 'Yeaaaaahhhhh boyyyyeeee! Successfully created the flavor!');
            return Redirect::to('flavors/' . $flavor->id . '/edit');
        }
    }
	
	public function create()
    {
		if(Auth::user()->group->id != 1){
			Session::flash('message', "Ah ah ah, you didn't say the magic word!");
			return Redirect::to('/');
		}
        // load the create form (app/views/nerds/create.blade.php)
        $ingredients 	= Ingredient::lists('name', 'id');
        $brands			= Brand::lists('name', 'id');
        return View::make('flavors.create', compact('manufacturers', 'ingredients', 'brands'));
    }
	
	public function delete($id){
		$flavor = Flavor::find($id);
		$flavor->delete();
		
		return Redirect::action('FlavorController@index');
	}
	
	public function destroy($id)
    {
        // delete
        $flavor = Flavor::find($id);
        $flavor->delete();

        // redirect
        Session::flash('message', 'Successfully deleted the flavor!');
        return Redirect::to('flavors');
    }

	public function getIngredients(){

		$flavors = Flavor::all();

		$flavArray = [];

		foreach ($flavors as $flavor){
			$ingArray = [];
			$flavArray[$flavor->id] = $ingArray;
			foreach ($flavor->ingredients as $ingredient){
				$flavArray[$flavor->id][$ingredient->id] = $ingredient->pivot->amount . " " . $ingredient->current_level;


			}

		}

		$json = json_encode($flavArray);

		echo $json;


//		echo "<pre>";
//		print_r($flavArray);
//		echo "</pre>";



	}

	public function dashboard(){



		return View::make('dashboard');
	}

	public function getNeeds(){



		$wvsNeeds = DB::connection('wvs')->select('call procJuice_Needed()');

//		$mvNeeds = DB::connection('mv')->select('call procJuice_Needed()');
//
//		$vmNeeds = DB::connection('vm')->select('call procJuice_Needed()');


//		echo "<pre>";
//		print_r($wvsNeeds);
//		echo "</pre>";
		$arr1 = []; //WVS

		$arr2 = []; // MV

		$arr3 = []; // VM

		$vmNeedsArray = [];

		$i = 0;
		foreach($wvsNeeds as $need){
			$arr1[$i]['name'] = $need->name;
			$arr1[$i]['sku'] = $need->sku;
			$arr1[$i]['needed'] = $need->needed;
			$i++;
		}

////		echo "<pre>";
////		print_r($arr1);
////		echo "</pre>";
//		$i = 0;
//		foreach($mvNeeds as $need){
//			$arr2[$need->sku]['name'] = $need->name;
//			$arr2[$need->sku]['sku'] = $need->sku;
//			$arr2[$need->sku]['needed'] = $need->needed;
//			$i++;
//		}
//		$i = 0;
//		foreach($vmNeeds as $need){
//			$arr3[$need->sku]['name'] = $need->name;
//			$arr3[$need->sku]['sku'] = $need->sku;
//			$arr3[$need->sku]['needed'] = $need->needed;
//			$i++;
//		}

		print_r(json_encode($arr1));



		die();


	}
	
}