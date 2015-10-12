<?php
//require_once $GLOBALS['dr']."classes/usermaster.php";

class organisations {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		

	$this->html .="<center>
		<table id=\"rowed3\"></table>
		<div id=\"prowed3\"></div>
		<br />
		<script type=\"text/javascript\">
		var lastsel;
		jQuery(\"#rowed3\").jqGrid({
			url:'ajax/jqgrid_organisations.php?q=2',
			datatype: \"xml\",
			colNames:['OrganisationID','OrganisationName', 'AccountType'],
			colModel:[
				{name:'OrganisationID', index:'OrganisationID', key:'true', width:200, align:\"center\", editable:false},
				{name:'OrganisationName',index:'OrganisationName', width:150, align:\"center\", editable:true},
				{name:'AccountType',index:'AccountType', width:100,align:\"center\", editable:true}				
				
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: '#prowed3',
			sortname: 'Login',
			viewrecords: true,
			sortorder: \"desc\",
			onSelectRow: function(Login){
				if(Login && Login!==lastsel){
					jQuery('#rowed3').jqGrid('restoreRow',lastsel);
					jQuery('#rowed3').jqGrid('editRow',Login,true);
					lastsel=Login;					
				}
			},
			editurl: \"ajax/jqgrid_edit_organisations.php\",			
			caption: \"System Users\"
		});
		jQuery(\"#rowed3\").jqGrid('navGrid',\"#prowed3\",{edit:true,add:false,del:false});
		</script>
	";
		return $this->html;
	}	
}
?>