<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

/*Menu Items*/
$main_menu_items=array("Home","Signup","Pricing","Features","Contact");
$main_menu_items_console=array("Home","Account","OrgUsers","Org","Help");
$main_menu_items_admin=array("Home","Users","Organisations");

if (ISSET($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == "localhost") {
	
	ini_set("include_path", "c:/xampp/htdocs/yoursite/pear/");
	/*Website URL*/
	$wb="http://localhost/yoursite/";
	/*Website Directory*/
	$dr="c:/xampp/htdocs/yoursite/";

	/*Database Type*/
	$database_type="mysql";
	/*Authentication Type*/
	$authentication_type="mysql";
	/*Database Server*/
	$database_hostname="localhost";
	/*Database Port*/
	$database_port="3306";
	/*Database User*/
	$database_user="root";
	/*Database Password*/
	$database_password="root";
	/*Database Name*/
	$database_name="yoursite";
	/*Database Prefix*/
	$database_prefix="yoursite.";
	
}
else {
	ini_set("include_path", "/var/www/html/yoursite/pear/");
	/*Website URL*/
	$wb="http://localhost/yoursite/";
	/*Website Directory*/
	$dr="/var/www/html/yoursite/";

	/*Database Type*/
	$database_type="mysql";
	/*Authentication Type*/
	$authentication_type="mysql";
	/*Database Server*/
	$database_hostname="dbserver";
	/*Database Port*/
	$database_port="3306";
	/*Database User*/
	$database_user="root";
	/*Database Password*/
	$database_password="";
	/*Database Name*/
	$database_name="yoursite";
	/*Database Prefix*/
	$database_prefix="yoursite.";
}
else {
	die("Site Config not setup correctly");
}
/* MAX FILE UPLOAD FOR PROFESSIONAL */
$max_org_file_upload_limit = 2500000;

/* STATUSES USED IN THE APP */
$statuses = array("inprogress","complete","issues");

/*Who should emails be sent from?*/
$email_recover_password_from="general@yoursite.com";

/*Register email from*/
$register_email_from="general@yoursite.com";

/*Mail Type either PHP's mail function or SMTP*/
$mail_type="smtp";
/*SMTP Server*/
$smtp_server="ssl://smtp.gmail.com";
$smtp_port=465;
$smtp_require_auth=true;
$smtp_user="general@yoursite.com";
$smtp_password="";

/* OTHER CONFIG */
$register_email_subject = "[yoursite] Registration";
$register_email_body    = "Welcome %username%,
You have been registered for yoursite.com, so please activate your account by clicking here:
".$wb."index.php?content=login&code=%code% 
%extra%
If you did not register, please ignore this email.

Regards, 
yoursite.com";
							
$forgot_email_subject = "[yoursite] Password Recovery";
$forgot_email_body    = "Hi,
Someone, perhaps you, requested your password to be recovered.
Click on this link ".$wb."index.php?content=reset&code=%code%

If you did not request this, please ignore this email. 

Regards,
yoursite.com";							
// EMAILEVERYONE
$email_task_complete_subject = "[yoursite] Task Status Change";
$email_task_complete_body    = "Hi %fullname%

This is an automatic update that task - '%taskname%' has a status change to '%statusname%'.

Regards,
yoursite.com";
// EMAILNEXT
$email_task_next_subject = "[yoursite] Start Your Task";
$email_task_next_body    = "Hi %fullname%,

This is an automatic update that your task - '%taskname%' can now begin as the previous task has been completed.

Regards, 
yoursite.com";
?>
