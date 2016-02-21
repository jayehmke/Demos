@extends('layout')

@section('content')

<div class="row">

<h1>Add a Flavor</h1>

<!-- if there are creation errors, they will show here -->

{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'flavors')) }}

    <div class="form-group">
        {{ Form::label('title', 'Title') }}
        {{ Form::text('title', Input::old('title'), array('class' => 'form-control', 'placeholder' => 'Title')) }}
    </div>
    <div class="form-group">
        {{ Form::label('body', 'Notes') }}
        {{ Form::text('body', Input::old('body'), array('class' => 'form-control', 'placeholder' => 'Notes')) }}
    </div>
    <div class="form-group">
	    {{ Form::label('brand', 'Brand') }}
	    {{ Form::select('brand', $brands, array('class' => 'form-control')) }}
	</div>
    <div class="form-group">
	    
	    {{ Form::label('ingredient_id', 'Ingredient') }}
	    <br />
		{{ Form::select('ingredient_id[]', $ingredients, array('class' => 'form-control'), array('multiple')) }}
	    <div>&nbsp;</div>
	</div>
	<br />
    {{ Form::submit('Create the Flavor!', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@stop