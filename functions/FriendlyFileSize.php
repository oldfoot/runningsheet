<?php
/* ensure this file is being included by a parent file */
defined( '_VALID_DIR_' ) or die( 'Direct Access to this location is not allowed.' );

function FriendlyFileSize($size) {
   // First check if the file exists.
   //if(!is_file($file)) exit("File does not exist!");
   // Setup some common file size measurements.
   $kb = 1024;         // Kilobyte
   $mb = 1048576;      // Megabyte
   $gb = 1073741824;   // Gigabyte
   $tb = 1099511627776;// Terabyte
   // Get the file size in bytes.
   //$size = filesize($file);
   /* If it's less than a kb we just return the size, otherwise we keep going until
   the size is in the appropriate measurement range. */
   if($size < $kb) {
       return $size." b";
   }
   else if($size < $mb) {
       return round($size/$kb,2)." KB";
   }
   else if($size < $gb) {
       return round($size/$mb,2)." MB";
   }
   else if($size < $tb) {
       return round($size/$gb,2)." GB";
   }
   else {
       return round($size/$tb,2)." TB";
   }
}
?>