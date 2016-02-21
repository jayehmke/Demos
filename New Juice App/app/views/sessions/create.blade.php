@extends('layout')

@section('content')



	{{ Form::open(array('route' => 'sessions.store', 'class'=>'form-signin')) }}

    <h2 class="form-signin-heading">Login</h2>
    {{ HTML::ul($errors->all()) }}
            <!-- check for login error flash var -->
    @if (Session::has('flash_error'))

        <script>

            $( document ).ready(function() {
                $('#errorModal').modal('toggle')
            });


        </script>

        <div class="modal fade" id="errorModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Error</h4>
                    </div>
                    <div class="modal-body">
                        <div id="flash_error">{{ Session::get('flash_error') }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        @endif

    <!-- username field -->
    <p>
        {{ Form::label('username', 'Username', array('class'=> 'sr-only')) }}<br/>
        {{ Form::text('username',null, array('class'=>'form-control inputText', 'placeholder'=>'Username', 'required')) }}
    </p>

    <!-- password field -->
    <p>
        {{ Form::label('password', 'Password', array('class'=> 'sr-only')) }}<br/>
        {{ Form::password('password', array('class'=>'form-control inputText', 'placeholder'=>'Password', 'required')) }}
    </p>

    <!-- submit button -->
    <p>{{ Form::submit('Login', array('class'=>'btn btn-lg btn-primary btn-block loginButton')) }}</p>

    {{ Form::close() }}


@stop