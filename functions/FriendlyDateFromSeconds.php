<?php
function FriendlyDateFromSeconds($dt,$onlytime=false,$showposneg=false) {
	//echo $dt."<br />"; // DEBUG
	$showsymbol = "";
	if ($showposneg) {
		if ($dt < 0) {
			$showsymbol = "-";
		}	
		else {
			$showsymbol = "+";
		}
	}
	$dt = abs($dt);
	if ($dt == 0) {
		return false;
	}
	elseif ($dt < 60) {
		if ($onlytime) {
			return "$dt secs";
		}
		else {
			return "< 1 min ago";
		}
	}
	elseif ($dt < 3600) {
		$min = round(($dt / 60),0);
		$plural = "";
		if ($min > 1) {
			$plural = "s";
		}		
		if ($onlytime) {
			return "$showsymbol$min mins";
		}
		else {
			return "$min min$plural ago";
		}
	}
	elseif ($dt < 86400) {
		$hour = round(($dt / 3600),0);
		$plural = "";
		if ($hour > 1) {
			$plural = "s";
		}		
		if ($onlytime) {
			return "$showsymbol$hour hour$plural";
		}
		else {
			return "$hour hour$plural ago";
		}
	}
	else {
		$days = round(($dt / 86400),0);		
		$plural = "";
		if ($days > 1) {
			$plural = "s";
		}		
		if ($onlytime) {
			return "$showsymbol$days day$plural";
		}
		else {
			return "$days day$plural ago";
		}
	}
}
?>