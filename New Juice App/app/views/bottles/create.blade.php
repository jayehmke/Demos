@extends('layout')

@section('content')

<div class="row">

<h1>Add a Bottle</h1>

<!-- if there are creation errors, they will show here -->

{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'bottles')) }}

    <div class="form-group">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
    </div>
	<div class="form-group">
        {{ Form::label('case_size', 'Case Size') }}
        {{ Form::text('case_size', Input::old('case_size'), array('class' => 'form-control')) }}
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
        {{ Form::label('alert_level', 'Alert Level') }}
        {{ Form::text('alert_level', Input::old('alert_level'), array('class' => 'form-control')) }}
    </div>

    {{ Form::submit('Create the Bottle!', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@stop