@extends('layout')

<style>

.noIng{
	background-color: #d9534f;
}

.yesIng {
	background-color: #ffffff;
}

@media (max-width: 1000px) {
    .bigMobile{
        height:75px;
    }
}

</style>


@section('content')


<script type='text/javascript'>//<![CDATA[
    $(document).ready(function(){

//        $(".confirm-delete").click(function(){
//            $('#confirm-delete').modal();
//        });

        $('#confirm-delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var flavor = button.data('flavor-id'); // Extract info from data-* attributes
            var flavorName = $("h3.flavor-"+flavor).text();
            var modal = $(this);
            modal.find('.modal-title').text('Confirm Delete');
            modal.find('.modal-body p').text("Are you sure you want to delete " + flavorName + "?");
            modal.find('.modal-delete-button').addClass(flavor);
            $('.btn-primary').prop('value', flavor);
        });

        $('.confirm-delete-button').click(function() {
            var flavorId = $(this).attr("value");
            $( "#form-" + flavorId ).submit();
        });

        refreshItems();
        $(function() {
            $(".make-button").click( function()
                    {
                        var idString = this.id;
                        var split = idString.split("-");
                        var id = split[0];
                        var qty = $("#" + id + "-qty").val();
                        $.ajax({
                            url:"/flavors/" + id + "/" + qty + "/make",
                            cache: false,
                            success: function(html){
                                var status = html;
                                if (status == 1){
                                    $("#juice-success").modal("show");
                                }
                                else {
                                    $("#juice-fail").modal("show");
                                }
                                refreshItems();
                            }
                        });
                    }
            );
        });

        function refreshItems(){
            $.ajax({
                url: "/flavors/1/getingredients",
                cache: false,
                success: function(html){
                    var json = html,
                            obj = JSON.parse(json);
                    $.each( obj, function( key, value ) {
                        var disableit = 0;
                        var flavorId = key;
                        $.each(value, function(key2, value2){
                            var ingredientId = parseInt(key2);
                            var split = value2.split(" ");
                            var amount = parseInt(split[0]);
                            var instock = parseInt(split[1]);
                            $("." + ingredientId + "-currentLevel").text(instock);
                            var myclass = $("#"+flavorId+"-ingRow-"+ingredientId).attr("class");
                            if (amount <= instock) {
                                $("#"+flavorId+"-ingRow-"+ingredientId).removeClass("noIng").addClass("yesIng");
                            }
                            else if (amount > instock){
                                $("#"+flavorId+"-ingRow-"+ ingredientId).removeClass("yesIng").addClass("noIng");
                                //$('#'+flavorId+'-button').attr('disabled','disabled');
                                disableit = 1;
                            }
                        });
                        if (disableit == 1){
                            $('#'+flavorId+'-button').attr('disabled','disabled');
                        }
                        else if (disableit == 0){
                            $('#'+flavorId+'-button').removeAttr('disabled');
                        }
                        //$("." + key + "-currentLevel").text(value);
                    });
                }
            });
            setTimeout(refreshItems, 5000);
        }

//        setInterval(function(){
//            $.ajax({
//                url: "/flavors/1/getingredients",
//                cache: false,
//                success: function(html){
//                    var json = html,
//                            obj = JSON.parse(json);
//                    $.each( obj, function( key, value ) {
//						var disableit = 0;
//                        var flavorId = key;
//                        $.each(value, function(key2, value2){
//
//                            var ingredientId = parseInt(key2);
//
//                            //var flavor = $(".flavor-" + key + "-ing-" + key2 + "-amt").text();
//
//							var split = value2.split(" ");
//
//                            var amount = parseInt(split[0]);
//                            var instock = parseInt(split[1]);
//
//                            $("." + ingredientId + "-currentLevel").text(instock);
//
//                            var myclass = $("#"+flavorId+"-ingRow-"+ingredientId).attr("class");
//
//                            //console.log(flavorId + " : " + ingredientId + " : " + split[0] + " : " + split[1]);
//
//                            if (amount <= instock) {
//                                $("#"+flavorId+"-ingRow-"+ingredientId).removeClass("noIng").addClass("yesIng");
//                                //$('#'+flavorId+'-button').removeAttr('disabled');
//                                //disableit = 0;
//                            }
//
//                            else if (amount > instock){
//
//	                            $("#"+flavorId+"-ingRow-"+ ingredientId).removeClass("yesIng").addClass("noIng");
//	                            //$('#'+flavorId+'-button').attr('disabled','disabled');
//	                            disableit = 1;
//                            }
//
//
//                        });
//						if (disableit == 1){
//							$('#'+flavorId+'-button').attr('disabled','disabled');
//						}
//						else if (disableit == 0){
//							$('#'+flavorId+'-button').removeAttr('disabled');
//                            console.log("Disable = " + disableit + " so enabling the button.");
//
//						}
//                        //$("." + key + "-currentLevel").text(value);
//                    });
//
//                }
//            });
//
//        }, 10000000);
    });
</script>

<div id="juice-fail" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" style="padding:30px;">
		  You cannot make this recipe with the current ingredient levels.
		</div>
	</div>
</div>
<div id="juice-success" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" style="padding:30px;">
		  You have successfully made this flavor.
		</div>
	</div>
</div>
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


	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">Flavors</div>




		    @foreach($flavors as $flavor)
			    	<div class="panel panel-default" id="{{ $flavor->title[0] }}" style="margin-top:40px">

			    		<div class="panel-heading" style="text-align:center;"><h3 class="flavor-{{$flavor->id}}">{{ $flavor->title }} - {{ $flavor->brand->name }}</h3></div>
			    		<div class="panel-body">
							<p style="text-align:center;">{{ $flavor->body }}</p>
						</div>
						<table class="table">
						<?php $disabled = 0; ?>
							@foreach($flavor->ingredients as $ingredient)
							<?php
								$class = "yesIng";

								if ($ingredient->pivot->amount > $ingredient->current_level){
									$disabled = 1;
									$class = "noIng";
							}
							?>
				        		<tr id="{{ $flavor->id }}-ingRow-{{ $ingredient->id }}" class="<?php echo $class; ?>">
                                    @if(Auth::user()->group->id == 1)
				        			<td><a href="/ingredients/{{ $ingredient->id }}/edit"><span style="color:#000;">Edit this ingredient</span></a></td>
                                    @endif
				        			<td style="text-align:right;">{{$ingredient->name}}</td>
				        			<td style="text-align:left;">
                                        <span class="flavor-{{ $flavor->id }}-ing-{{ $ingredient->id }}-amt"> <?php if (!$ingredient->pivot->amount){$disabled = 1;}?> {{ $ingredient->pivot->amount }}</span>
                                    </td>
				        			<td>current level: <span class="{{ $ingredient->id }}-currentLevel">{{ $ingredient->current_level}}</span>
				        			<input type="hidden" id="flavor-{{ $flavor->id }}-ing-{{ $ingredient->id }}-currentLevel" value="{{ $ingredient->current_level }}">
				        			</td>
				        		</tr>

							@endforeach
							<input type="hidden" id="<?php echo $flavor->id . "-ings" ; ?>" value="<?php foreach ($flavor->ingredients as $ingredient){echo $ingredient->id . ",";}?>" >
						</table>
						<div class="row">

							<div class="col-xs-4">&nbsp;</div>

							<div class="col-xs-3">
								<div class="input-group">
							      <input id="{{ $flavor->id }}-qty" type="text" value="1" class="form-control" placeholder="1">
							      <span class="input-group-btn">
								        <button id="{{ $flavor->id }}-button" type="button" class="make-button btn btn-default" <?php if ($disabled == 1): ?>disabled = "disabled"<?php endif; ?>>
										    Make This Recipe
										</button>

							      </span>
							    </div><!-- /input-group -->
							</div>
						</div>

						<div class="row">

							<div class="col-xs-12">&nbsp;</div>

						</div>

						<div class="btn-group btn-group-justified" role="group">
							@if(Auth::user()->group->id == 1)
                                <div class="col-xs-6">
						  	<a href="{{ URL::to('flavors/' . $flavor->id . '/edit') }}" class="btn btn-success btn-lg" style="width:100%">Edit Flavor</a>
                                </div>
                            <div class="col-xs-6"> 
                                <button id="{{$flavor->id}}" type="button" class="confirm-delete btn-danger btn-lg" style="width:100%;" data-flavor-id="{{$flavor->id}}" data-toggle="modal" data-target="#confirm-delete">
                                    Delete Flavor
                                </button>
                                <span style="display:none;">
                                {{ Form::open(array('route' => array('flavors.destroy', $flavor->id), 'method' => 'delete', 'id'=> "form-".$flavor->id)) }}
                                <button type="submit" class="btn btn-danger btn-lg" style="width: 100%;">Delete</button>
                                {{ Form::close() }}
                                </span>
                            </div>
							@endif
						</div>
			    	</div>
			@endforeach
		</div>
	</div>
</div>
<nav class="navbar navbar-default navbar-fixed-bottom">
    <div class="container-fluid">

        <div class="collapse navbar-collapse">
            <div class="btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
                <?php
                foreach ($flavorHalfOne as $flavoritem): ?>
                <div class="btn-group" role="group">
                    <button onclick="window.location.href='?flava=<?php echo $flavoritem; ?>'" type="button" class="bigMobile btn btn-default navbar-btn"><?php echo $flavoritem; ?></button>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
                <?php foreach ($flavorHalfTwo as $flavoritem): ?>
                <div class="btn-group" role="group">
                    <button onclick="window.location.href='?flava=<?php echo $flavoritem; ?>'" type="button" class="bigMobile btn btn-default navbar-btn"><?php echo $flavoritem; ?></button>
                </div>
                <?php endforeach; ?>
            </div>

        </div>


    </div>
</nav>
@stop