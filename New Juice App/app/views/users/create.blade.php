@extends('layout')

@section('content')

<div class="row">

<h1>Add a User</h1>

<!-- if there are creation errors, they will show here -->

{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'users')) }}

    <div class="form-group">
        {{ Form::label('username', 'User Name') }}
        {{ Form::text('username', Input::old('username'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('email', 'Email Address') }}
        {{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
    </div>
    <div class="form-group">
        {{ Form::label('password', 'Password') }}
        {{ Form::password('password', null, array('class' => 'form-control')) }}
    </div>
	<div class="form-group">
        {{ Form::label('group', 'Group') }}
        {{ Form::select('group_id', $groups, array('class' => 'form-control')) }}
    </div>


    {{ Form::submit('Create the User!', array('class' => 'btn btn-primary')) }}

{{ Form::close() }}

</div>

@stop