<?php
define( '_VALID_DIR_', 1 );
require "config.php";

// DESTROY THE ENTIRE SESSION DATA
session_destroy();

header("location: index.php");
?>