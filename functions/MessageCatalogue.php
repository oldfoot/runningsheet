<?php
function MessageCatalogue($id) {
	// TODO: CHANGE TO STORED PROC
	
	$db = $GLOBALS['db'];
	$db->NextResult();
	$sql = "SELECT MessageID, Message
			FROM messagecatalogue
			WHERE MessageID = $id
			";
	$result = $db->Query($sql);
	while ($row = $db->FetchArray($result)) {
		return $row['MessageID']." - ".$row['Message'];
	}	
}
?>