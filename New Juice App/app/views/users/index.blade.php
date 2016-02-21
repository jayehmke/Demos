@extends('layout')

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">Users</div>
			<table class="table table-striped">
				<tr>
					<td>Username</td>
					<td>Email</td>
					<td>Group</td>
					<td>Actions</td>
				</tr>
		    @foreach($users as $user)
		        <tr>
			        <td>{{ $user->username }}</td>
			        <td>{{ $user->email }}</td>
			        <td>{{ $user->group->name }}</td>
			        <td>
				        <a class="btn btn-primary btn-lg" href="{{ URL::to('users/' . $user->id . '/edit') }}" role="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
				        <a class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
				       
						{{ link_to_route('users.destroy', 'D', $user->id, ['data-method'=>'delete']) }}

		                
			        </td>
		        </tr>
		        
		    @endforeach
			</table>
		</div>
	</div>
</div>

<script>

jQuery('.delete-event').click(function(event) {
            var href = jQuery(this).attr('href');
            var message = jQuery(this).attr('data-content');
            var title = jQuery(this).attr('data-title');

            if (!jQuery('#dataConfirmModal').length) {
                jQuery('body').append('<div id="dataConfirmModal" class="modal fade" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title">'+title+'</h4></div><div class="modal-body"><p>One fine body&hellip;</p></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary">Yes, Delete It.</button></div></div></div><</div>');
            } 

            jQuery('#dataConfirmModal').find('.modal-body').text(message);
            jQuery('#dataConfirmOK').attr('href', href);
            jQuery('#dataConfirmModal').modal({show:true});
})
	
</script>



@stop

