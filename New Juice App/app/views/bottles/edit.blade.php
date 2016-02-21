@extends('layout')

@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>Edit {{ $bottle->name }}</h1>

		{{ HTML::ul($errors->all()) }}
		
		{{ Form::model($bottle, array('route' => array('bottles.update', $bottle->id), 'method' => 'PUT')) }}
		
		    <div class="form-group">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', null, array('class' => 'form-control')) }}
		    </div>
			<div class="form-group">
		        {{ Form::label('case_size', 'Case Size') }}
		        {{ Form::text('case_size', null, array('class' => 'form-control')) }}
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

		
		    {{ Form::submit('Edit the Bottle!', array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
	</div>
</div>
@stop

