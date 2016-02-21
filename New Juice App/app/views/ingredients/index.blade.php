@extends('layout')

@section('content')

<script>
	$(document).ready(function(){
	 	$('#confirm-delete').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget); // Button that triggered the modal
            var ingredient = button.data('ingredient-id'); // Extract info from data-* attributes
            var ingName = $("#"+ingredient+"-name").text();
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);
            modal.find('.modal-title').text('Confirm Delete');
            modal.find('.modal-body p').text("Are you sure you want to delete " + ingName + "? It may be assigned to some flavors. Deleting this ingredient will remove it from all flavors that have it assigned.");
            modal.find('.modal-delete-button').addClass(ingredient);
            $('.btn-primary').prop('value', ingredient);
        });

        $('.confirm-delete-button').click(function() {
            var flavorId = $(this).attr("value");
            $( "#form-" + flavorId ).submit();
        });

		(function ($) {

			$('#filter').keyup(function () {

				var rex = new RegExp($(this).val(), 'i');
				$('.searchable tr').hide();
				$('.searchable tr').filter(function () {
					return rex.test($(this).text());
				}).show();

			})

		}(jQuery));

    });

</script>

<div id="confirm-delete" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" value="#" class="confirm-delete-button btn btn-primary">Yes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">Ingredients</div>
			<div class="input-group"> <span class="input-group-addon">Filter</span>

				<input id="filter" type="text" class="form-control" placeholder="Type here...">
			</div>
			<table class="table table-striped">
				<thead>
				<tr>
					<th>Ingredient name</th>
					<th>Current Level</th>
					<th>Par Level</th>
					<th>Alert Level</th>
					<th>Reorder Level</th>
					<th>Manufacturer</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody class="searchable">
		    @foreach($ingredients as $ingredient)
		        <tr>
			        <td><span id="{{$ingredient->id}}-name">{{ $ingredient->name }}</span></td>
			        <td>{{ $ingredient->current_level}}</td>
			        <td>{{ $ingredient->par_level}}</td>
			        <td>{{ $ingredient->alert_level}}</td>
			        <td>{{ $ingredient->reorder_level}}</td>
			        <td>{{ $ingredient->manufacturer->name}}</td>
			        <td>
				        <a class="btn btn-primary btn-lg" href="{{ URL::to('ingredients/' . $ingredient->id . '/edit') }}" role="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
						<button id="{{$ingredient->id}}" type="button" class="glyphicon glyphicon-trash confirm-delete btn-danger btn-lg" style="" data-ingredient-id="{{$ingredient->id}}" data-toggle="modal" data-target="#confirm-delete">

						</button>
						<span style="display:none;">
							{{ Form::open(array('route' => array('ingredients.destroy', $ingredient->id), 'method' => 'delete', 'id'=> "form-".$ingredient->id)) }}
							<button type="submit" class="btn btn-danger btn-lg" style="width: 100%;">Delete</button>
							{{ Form::close() }}
						</span>
			        </td>
		        </tr>

		    @endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@stop

