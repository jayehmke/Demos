@extends('layout')

@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>Edit {{ $brand->name }}</h1>

		{{ HTML::ul($errors->all()) }}
		
		{{ Form::model($brand, array('route' => array('brands.update', $brand->id), 'method' => 'PUT')) }}
		
		    <div class="form-group">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', null, array('class' => 'form-control')) }}
		    </div>

		
		    {{ Form::submit('Edit the Brand!', array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
	</div>
</div>
@stop

