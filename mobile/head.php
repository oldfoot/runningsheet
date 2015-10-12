<html class="ui-mobile landscape min-width-320px min-width-480px min-width-768px min-width-1024px">
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, minimum-scale=1, maximum-scale=1'>
<link rel='stylesheet'  href='include/jquerymobile/jquery.mobile-1.0a4.1.min.css' />
<link rel="stylesheet"  href="include/jquerymobile/jquery.ui.datepicker.mobile.css" />
<link rel='stylesheet' href='css/jqm-docs.css' />
<script type='text/javascript' src='include/jquerymobile/jquery-1.5.min.js'></script>
<script type='text/javascript' src='include/jquerymobile/jquery.mobile.themeswitcher.js'></script>
<script>
		//reset type=date inputs to text
		$( document ).bind( "mobileinit", function(){
			$.mobile.page.prototype.options.degradeInputs.date = true;
		});	
	</script>
<script type='text/javascript' src='include/jquerymobile/jquery.mobile-1.0a4.1.min.js'></script>
<script src="include/jquerymobile/jQuery.ui.datepicker.js"></script>
<script src="include/jquerymobile/jquery.ui.datepicker.mobile.js"></script>
</head>
<body>
<div data-role="header">
	<h1>jQuery UI's Datepicker Styled for mobile</h1>		
</div>