@extends('layout')

@section('content')

<div class="row">

<h1>Add an Ingredient</h1>

<!-- if there are creation errors, they will show here -->

{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'ingredients')) }}

    <div class="form-group">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('current_level', 'Current Level') }}
        {{ Form::text('current_level', Input::old('current_level'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('par_level', 'Par Level') }}
        {{ Form::text('par_level', Input::old('par_level'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('reorder_level', 'Reorder Level') }}
        {{ Form::text('reorder_level', Input::old('reorder_level'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('alert_level', 'Alert Level') }}
        {{ Form::text('alert_level', Input::old('alert_level'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('qty_on_order', 'Quantity on Order') }}
        {{ Form::text('qty_on_order', Input::old('qty_on_order'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('manufacturer_id', 'Manufacturer') }}
        {{ Form::select('manufacturer', $manufacturers, array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('comments', 'Comments') }}
        {{ Form::text('comments', Input::old('comments'), array('class' => 'form-control')) }}
    </div>

    {{ Form::submit('Create the Ingredient!', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@stop