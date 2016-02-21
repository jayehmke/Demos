@extends('layout')

@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>Edit {{ $user->name }}</h1>

		{{ HTML::ul($errors->all()) }}
		
		{{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
			
			<div class="form-group">
		        {{ Form::label('name', 'Name') }}
		        {{ Form::text('name', null, array('class' => 'form-control')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('username', 'User Name') }}
		        {{ Form::text('username', null, array('class' => 'form-control', 'disabled')) }}
		    </div>
		    <div class="form-group">
		        {{ Form::label('email', 'Email') }}
		        {{ Form::text('email', null, array('class' => 'form-control')) }}
		    </div>
			<div class="form-group">
		        {{ Form::label('password', 'Password') }}
		        {{ Form::password('password', null, array('class' => 'form-control')) }}
		    </div>

        @if(Auth::user()->group->id == 1)

		    <div class="form-group">
		        {{ Form::label('group', 'Group') }}
		        {{ Form::select('group_id', $groups, $user->group->id, array('class' => 'form-control')) }}
		    </div>
        @endif

        @if(Auth::user()->group->id < 3)
		    
		    <h3>Juice Lines</h3>

			<div class="form-group">
				
				@foreach ($hasBrands as $brand)
					
					@if($brand->has_access == 1)
						
						{{-- */$has_access = true;/* --}}
						
						
					@else
						
						{{-- */$has_access = false;/* --}}
					
					@endif
					
					
					{{ Form::checkbox('brand[]', $brand->id, $has_access) }}
					{{ Form::label('brand', $brand->name) }}
					<br />
					
				@endforeach
			
			</div>
        @endif
		    {{ Form::submit('Edit the User!', array('class' => 'btn btn-primary')) }}
		
		{{ Form::close() }}
	</div>
</div>
@stop

