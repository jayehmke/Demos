@extends('layout')

@section('content')

<div class="row">

<h1>Add a Manufacturer</h1>

<!-- if there are creation errors, they will show here -->

{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'manufacturers')) }}

    <div class="form-group">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
    </div>


    {{ Form::submit('Create the Manufacturer!', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@stop