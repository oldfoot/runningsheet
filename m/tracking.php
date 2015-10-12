<!DOCTYPE html>
<html class="ui-mobile"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"><!-- base href="http://jquerymobile.com/test/docs/pages/multipage-template.html" -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<title>Multi-page template</title> 
	<link rel="stylesheet" href="jquery/jquery.css">
	<script src="jquery/jquery164.js"></script>
	<script src="jquery/jquerymobile.js"></script>
</head> 

<body class="ui-mobile-viewport"> 
<div style="min-height: 569px;" class="ui-page ui-body-c ui-page-active" tabindex="0" data-url="one" data-role="page" id="one">

	<div role="banner" class="ui-header ui-bar-a" data-role="header">
		<h1 aria-level="1" role="heading" tabindex="0" class="ui-title">RunningSheet Mobile</h1>
	</div>

	<p class="intro">Event Tracking</p>
	<p>Tracking: + 4 hours</p>
	<p>Estimated End: 15:40</p>
	<p>Current Task/s: Task 14/20 - Deploy to test server</p>
	<p>Next Task: Blah</p>
	<input onClick="document.location.href='event.php'" type="button" value="Back" class="ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" data-theme="c" data-role="button">
	
	<div role="contentinfo" class="ui-footer ui-bar-d" data-role="footer" data-theme="d">
		<h4 aria-level="1" role="heading" tabindex="0" class="ui-title"><a href="logout.php">Logout</a></h4>
	</div>
</div>
</body>
</html>