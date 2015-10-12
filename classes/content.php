<?php
class Content {
	public function __construct() {
		$this->html = "";
		$this->show_footer_banners = true;
		$this->filename = $_SERVER['SCRIPT_NAME'];
		$this->show_content = true;
	}
	public function SetVar($var,$val) {
		$this->$var = $val;	
	}
	public function GetVar($var) {
		if (ISSET($this->$var)) {
			return $this->$var;
		}
	}
	public function Head() {
		
		$this->html .= "<!DOCTYPE html>
						<html lang='en'>
						<head>
						<title>Home</title>
						<meta charset='utf-8'>
						<link rel='stylesheet' href='css/reset.css' type='text/css' media='all'>
						<link rel='stylesheet' href='css/layout.css' type='text/css' media='all'>
						<link rel='stylesheet' href='css/style.css' type='text/css' media='all'>
						<link rel='stylesheet' href='css/ui-lightness/jquery-ui-1.8.16.custom.css' type='text/css' media='all'>
						<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js' ></script>
						<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js' ></script>
						
						<script type='text/javascript' src='jquery/plugins/jquery.form.js'></script>
						
						<script type='text/javascript' src='js/cufon-yui.js'></script>
						<script type='text/javascript' src='js/cufon-replace.js'></script>
						<script type='text/javascript' src='js/Myriad_Pro_400.font.js'></script>
						<script type='text/javascript' src='js/Myriad_Pro_700.font.js'></script>
						<script type='text/javascript' src='js/Myriad_Pro_600.font.js'></script>
						<!--[if lt IE 9]>
							<script type='text/javascript' src='http://info.template-help.com/files/ie6_warning/ie6_script_other.js'></script>
							<script type='text/javascript' src='js/html5.js'></script>
						<![endif]-->
						
						<link rel='stylesheet' type='text/css' media='screen' href='include/jqgrid/css/ui.jqgrid.css' />    					
					
						
						<script src='include/jqgrid/js/i18n/grid.locale-en.js' type='text/javascript'></script>
						<script src='include/jqgrid/js/jquery.jqGrid.min.js' type='text/javascript'></script>	
						
						
						</head>";
	}
	
	public function Body() {
	
	$this->html .= "<body id='page1'>
					<center>NOTE: This is a beta software in experimentation. If you like it, we'd like to hear from you: general [at] runningsheet.com<br /></center>
					<div class='main'>
					<!-- header -->
						<header>
							<div class='wrapper'>
								";
								$logo = "bin/logo.php";								
								if (preg_match("/index.php/",$this->filename)) {
									$logo = "images/logo.gif";
								}
	
	$this->html .= "
								<h1><a href='".$this->filename."' id='logo' style='display:block;background:url(".$logo.") no-repeat;width:245px;height:85px;text-indent:-5000px'>RunningSheet.com</a></h1>
								<div class='bg' id='login'>
								";
								if (ISSET($_SESSION['userid'])) {
	$this->html .=					"<a href='console.php?content=account'>My Account</a> |\n
									<a href='app.php'>Back to App</a> |\n
									<a href='logout.php'>Signout</a>\n
									";
								}
								else {								
	$this->html .=				"<form action='index.php?content=login' method='post'>
									
										Email: <input type='text' name='userlogin' value=''>
										Password:<input type='password' name='password' value=''>
										<input type='submit' value='Go'>
										<br />
										
										<p style='float:right'><a href='m/'>Mobile</a> | <a href='index.php?content=pricing'>Signup</a> | <a href='index.php?content=forgot'>Forgot Password?</a></p>									
								</form>";
								}
								
	$this->html .=				"</div>
							</div>
							<nav>
								<ul id='menu'>";
									$main_menu_items = $this->menu_items;
									for ($i=0;$i<count($main_menu_items);$i++) {
										$class = "";
										if ($i == 0) { $class = "class='alpha'"; } elseif ($i == count($main_menu_items)-1) { $class="class='omega'";  }
										$sel = "";
										if (ISSET($_GET['content']) && $_GET['content'] == strtolower($main_menu_items[$i])) { $sel = "id='menu_active'"; }
										$this->html .= "<li $class $sel><a href='".$this->filename."?content=".strtolower($main_menu_items[$i])."'><span><span>".$main_menu_items[$i]."</span></span> </a></li>\n";
									}
									
									
	$this->html .= 			"</ul>
							</nav>
							<div class='wrapper'>";
								
								if (ISSET($_GET['content']) && $_GET['content'] != "home") {
									$f = $GLOBALS['dr']."content/".$_GET['content'].".php";
									if (file_exists($f)) {
										require $f;
										$class = $_GET['content'];
										
										$content = new $class;
										$this->html .= $content->HTML();										
									}
								}
								else {
									if ($this->show_content) {
									$this->html .= "<div class='text'>
											<span class='text1'>Electronic<span>task management</span></span>
											<a href='index.php?content=features' class='button'>read more</a>
											</div>";
									}
								}
								if ($GLOBALS['errors']->AlertCount() > 0) {
									$this->html .= "<script>
													$(function() {
														// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
														$( \"#dialog:ui-dialog\" ).dialog( \"destroy\" );
													
														$( \"#dialog-modal\" ).dialog({
															height: 140,
															modal: true
														});
													});
													</script>";
									//$this->html .= "<div id=\"dialog\" title=\"Dialog Title\">I'm in a dialog</div>";
									$this->html .= "<div id='dialog-modal' title='Alert'>\n";
									$this->html .= $GLOBALS['errors']->GetAlerts();
									$this->html .= "</div>\n";									
								}			
	
	// CONTENT
	if (!ISSET($_GET['content']) && $this->show_content) {
	$this->html .= "<div class='pad'>
				<div class='wrapper'>
					<article class='col1'><h2>Run Sheets</h2></article>
					<article class='col2 pad_left1'><h2>Key Features</h2></article>
				</div>

			</div>
			<div class='box pad_bot1'>
				<div class='pad marg_top'>
					<article class='col1'>
						<div class='wrapper'>
							<figure class='left marg_right2'><img src='images/page3_img1.jpg' alt=''></figure>
							<p class='pad_bot3'><strong>Definition: A list of procedures or events run in a sequence</strong></p>
							<p>Running sheets are useful where multiple teams might be located in different geographical areas and need to coordinate in an event.</p>

						</div>
						<p>When a task completes for example, alert the next task owner that they can begin work! Or if things start going off track, send alerts out to the entire team.</p>
						<p>Get a management view of the event - are you on track, how far behind schedule are you? Can you make up for lost time?</p>
					</article>
					<article class='col2 pad_left1'>
						<div class='wrapper'>
							<div class='wrapper pad_bot1'>
								<figure class='left marg_right1'><a href='#'><img src='images/cloud_computing.jpg' alt=''></a></figure>

								<p>Cloud computing - no hardware investment, pricing to suit.</p>
								<a href='#' class='marker'></a>
							</div>
							<div class='wrapper pad_bot1'>
								<figure class='left marg_right1'><a href='#'><img src='images/odometer.jpg' alt=''></a></figure>
								<p>Get odometer style views of your event to track progress.</p>
								<a href='#' class='marker'></a>
							</div>

							<div class='wrapper'>
								<figure class='left marg_right1'><a href='#'><img src='images/waterfallchart.jpg' alt=''></a></figure>
								<p>Cumulative event progress</p>
								<a href='#' class='marker'></a>
							</div>
						</div>
					</article>
				</div>

			</div>";
	}
	// BANNERS AT THE BOTTOM
	if ($this->show_footer_banners) {
		$this->html .= 	"</div>
							</header>
						<!-- / header -->
						<!-- content -->
							<section id='content'>
								<div class='wrapper'>
									<div class='wrapper'>
										<ul class='banners'>\n";
										
										$sql = "CALL sp_banners_browse()";
										
										$result = $GLOBALS['db']->Query($sql);
										if ($result) {
											while ($row = $GLOBALS['db']->FetchArray($result)) {
												$this->html .= $row['Content'];
											}
										}
										
		$this->html .= "									
											
										</ul>
									</div>			
								</div>
							</section>
						<!-- / content -->";
	}
	$this->html .= "
					<!-- footer -->
						<footer>
							<a rel='nofollow' href='http://www.genusproject.org/' target='_blank'>Sponsored by genusproject.org<br>		
						</footer><!-- / footer -->
					</div>
					</body>
					<script type=\"text/javascript\">
					  var _gaq = _gaq || [];
					  _gaq.push(['_setAccount', 'UA-27633727-1']);
					  _gaq.push(['_trackPageview']);

					  (function() {
						var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
						ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
					  })();
					</script>
					</html>";
	}
	
}
?>