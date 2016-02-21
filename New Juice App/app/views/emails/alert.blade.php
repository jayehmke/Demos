<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{$message->ingredient}} is at or Below Alert Level!</h2>

		<div>{{$message->ingredient}} has {{$message->current_level}} available. Please place an order for more and mark this ingredient as ordered to silence these alerts.

		</div>
</body>
</html>