<?php
//require_once $GLOBALS['dr']."classes/usermaster.php";

class users {

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
			url:'ajax/jqgrid_users.php?q=2',
			datatype: \"xml\",
			colNames:['Login','FullName', 'Activated', 'DateTimeCreated', 'Timezone'],
			colModel:[
				{name:'Login', index:'Login', key:'true', width:200, align:\"center\", editable:false},
				{name:'FullName',index:'FullName', width:150, align:\"center\", editable:false},				
				{name:'Activated',index:'Activated', width:100,align:\"center\", editable:true},
				{name:'DateTimeCreated',index:'DateTimeCreated', width:150,align:\"center\", editable:true},
				{name:'Timezone',index:'Timezone', width:120,align:\"center\", editable:true}
				
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
			editurl: \"ajax/jqgrid_edit_orgusers.php\",			
			caption: \"System Users\"
		});
		jQuery(\"#rowed3\").jqGrid('navGrid',\"#prowed3\",{edit:true,add:false,del:false});
		</script>
	";
		return $this->html;
	}	
}
?>