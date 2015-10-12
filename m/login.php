<?php
define( '_VALID_DIR_', 1 );

require "config.php";
require "header.php";

$message = "";
if (ISSET($_POST['userlogin']) && ISSET($_POST['password'])) {	
	require "../content/login.php";
	$login = new login;
	$login->SetVar("debug",true);
	$result = $login->Process();
	if ($result) {
		header("location: events.php");
	}
	else {
		$message = "<p class='header'>Failed to login</p>";
	}
}
echo $message;
?>

<div role="main" class="ui-content" data-role="content">
	<form method="post" action="login.php">
	<h2>Login Here</h2>
		<label for="username">Username:</label>
		<input type="text" name="userlogin" />
		<label for="password">Password:</label>
		<input type="password" name="password" />			
		<p><input type="submit" value="Login" class="ui-btn ui-btn-corner-all ui-shadow ui-btn-up-c" data-theme="c" data-role="button"></p>
	</form>
</div>
<?php
require "footer.php";
?>