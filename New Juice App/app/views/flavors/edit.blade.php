@extends('layout')

@section('content')
<div class="row">
	<div class="col-md-9">
		<h1>Edit {{ $flavor->name }}</h1>

		{{ HTML::ul($errors->all()) }}
		
		
		
		{{ Form::model($flavor, array('route' => array('flavors.update', $flavor->id), 'method' => 'PUT')) }}
		
		    <div class="form-group">
		        {{ Form::label('title', 'Title') }}
		        {{ Form::text('title', null, array('class' => 'form-control')) }}
		    </div>
		    
		    <div class="form-group">
		        {{ Form::label('brand', 'Brand') }}
		        {{ Form::select('brand', $brands, $flavor->brand->id, array('class' => 'form-control')) }}
		    </div>
		    
		    <div class="row">
		    
		    <div class="col-sm-3">
		    Ingredient Name
		    </div>
		    
		    <div class="col-sm-3">
		    Ingredient Amount
		    </div>
		    
		    <div class="col-sm-3">
		    Delete Ingredient?
		    </div>
		    
		    </div>
		    
		    @foreach($flavor->ingredients as $ingredient)
		    
		    <div class="form-group">
			    <div class="row">

			        <div class="col-sm-3">
			        {{ Form::select('ingredient[]', $ingredients, $ingredient->id, array('class' => 'form-control')) }}
			        </div>

			        <div class="col-sm-3">
			        {{ Form::text('amount[]', $ingredient->pivot->amount, array('class' => 'form-control', 'placeholder' => 'Ingredient Amount')) }}
			        </div>
			        <div class="col-sm-3">
			        {{ Form::checkbox('deleteIng[]', $ingredient->id) }}
			        </div>
			    </div>
		    </div>

		    @endforeach
		    
		    <div class="form-group">
		    	{{ Form::label('ingredients', 'Add More Ingredients') }}<br />
		    	{{ Form::select('ingredient_add[]', $ingredients, array('class' => 'form-control'), array('multiple')) }}
		    
		    </div>
		    
			<div class="form-group">
			    {{ Form::label('body', 'Notes') }}
		        {{ Form::text('body', $flavor->body, array('class' => 'form-control')) }}
		    </div>
		    {{ Form::submit('Edit the Flavor!', array('class' => 'btn btn-primary')) }}
		    
		
		{{ Form::close() }}
	</div>
</div>
@stop

