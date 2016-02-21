@extends('layout')

@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>Edit {{ $ingredient->name }}</h1>

		{{ HTML::ul($errors->all()) }}
		
		{{ Form::model($ingredient, array('route' => array('ingredients.update', $ingredient->id), 'method' => 'PUT')) }}
		
		    <div class="form-group">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('current_level', 'Current Level') }}
		        {{ Form::text('current_level', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('par_level', 'Par Level') }}
		        {{ Form::text('par_level', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('alert_level', 'Alert Level') }}
		        {{ Form::text('alert_level', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('qty_on_order', 'Quantity on Order') }}
		        {{ Form::text('qty_on_order', null, array('class' => 'form-control')) }}
		    </div>
			<div class="form-group">
				{{ Form::label('is_on_order', 'On Order?') }}
				{{ Form::select('is_on_order', array(1 => 'Yes', 0 => 'No'), $ingredient->is_on_order, array('class'=>'form-control')) }}
			</div>
		    <div class="form-group">
		        {{ Form::label('reorder_level', 'Reorder Level') }}
		        {{ Form::text('reorder_level', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('manufacturer', 'Manufacturer') }}
		        {{ Form::select('manufacturer', $manufacturers, $ingredient->manufacturer_id, array('class' => 'form-control')) }}
		    </div>
			<div class="form-group">
		        {{ Form::label('comments', 'Comments') }}
		        {{ Form::textarea('comments', null, array('class' => 'form-control')) }}
		    </div>
			<div class="form-group">
		        {{ Form::label('notes', 'Notes') }}
		        {{ Form::textarea('notes', null, array('class' => 'form-control')) }}
		    </div>

		
		    {{ Form::submit('Edit the Ingredient!', array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
	</div>
</div>
@stop

