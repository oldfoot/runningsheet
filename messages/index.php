<!DOCTYPE html>
<html>
<head>
  <style>
  p { background:yellow; }
</style>
  <script src="../jquery/jquery-1.6.4.min.js"></script>
</head>
<body>
  <div id="msg">123</div>
<script>
	$('#msg').append('test');

	$.ajax({
		type: "POST",
		url: "some.php",
		data: "name=John&location=Boston",
		}).done(function( msg ) {
			alert( "Data Saved: " + msg );
		});
</script>

</body>
</html>