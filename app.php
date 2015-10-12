<?php
define( '_VALID_DIR_', 1 );
require "config.php";
if (!ISSET($_SESSION['userid'])) {
	header("Location: index.php");
}
echo $user->GetVar("RoleName");
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>RunningSheet.com</title>
	<link rel="stylesheet" href="css/app.css" type="text/css" media="all">
	<link rel="stylesheet" href="css/threecol.css" type="text/css" media="all">
	<link type="text/css" href="jquery/themes/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	<link type="text/css" href="jquery/themes/ui-lightness/timepicker.css" rel="stylesheet" />
	
	<script type="text/javascript" src="jquery/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="jquery/plugins/jquery-ui-timepicker-addon.js"></script>	
	<script type="text/javascript" src="jquery/plugins/jquery.form.js"></script>
	
	<script language="javascript" type="text/javascript" src="include/jqplot/jquery.jqplot.min.js"></script>
	
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="include/jqplot/plugins/jqplot.meterGaugeRenderer.min.js"></script>

</head>
<body>
<script>
var global_eventid = 0;
var global_event_count = 0;
var global_taskid  = 0;
$(document).ready(function() {	
	$("#loading").hide();
	$("#event_more").hide();
	$("#task_buttons").hide();
	$("#task_create_form").hide();
	$("#task_ajax").hide();	
	$("#div_tasks").hide();
	$("#post_message").hide();
	$("#create_event_form").hide();
	$("#task_create_form").hide();	
	$("#add_resource_form").hide();
	$("#upload_file").hide();
	
})

setTimeout('AjaxEvents()', 5000);


function AjaxEvents() {
	//alert('Ajax Events Firing');
	$.ajax({
	  url: "ajax/ajaxevents.php?eventid="+global_eventid,
	  cache: false,
	  success: function(html){		
		var pieces = html.split(",");		
		for (i=0;i<pieces.length;i++) {
			if (pieces[i] == "events") {
				//alert("Refreshing Tasks");
				AjaxLoadTasks(global_eventid,'');
			}			
		}
		//alert(html);
	  }
	});
	setTimeout('AjaxEvents()', 5000);
}

function ToggleDiv(id,status) {	
	if (status == "show") {		
		$('#'+id).fadeIn('slow');		
	}
	else {		
		$('#'+id).fadeOut('slow');
	}
}
function CreateNewEvent() {	
	ResetEventForm();
	$("#loading").show();	
	$("#post_message").hide();
	$("#event_more").hide();	
	$("#task_buttons").hide();
	$("#task_create_form").hide();
	$("#task_ajax").hide();
	$("#task_home").show();
	$('#events').fadeOut('slow');	
	$('#create_event_form').fadeIn('slow');	
	$("#loading").hide();
	
}
function CancelNewEvent() {	 
	 $('#create_event_form').fadeOut('fast');	 
	 $('#events').fadeIn('fast');
}
function CreateNewTask() {
	 $('#task_ajax').fadeOut('slow');
	 $('#task_create_form').fadeIn('slow');
	 $('#taskid').val('');
	 PopulateDropDown("json_resources","resourceid","eventid="+global_eventid);
	 PopulateDropDown("json_dependencies","dependencyid","eventid="+global_eventid);
}
function ResetEventForm() {
	$('#eventname').val('');
	$('#eventdatetimestart').val('');
	$('#eventdatetimeend').val('');
	$('input:checkbox[name=locked]').attr('checked',false);
}
function ResetTaskForm() {
	$('#taskid').val('');
	$('#taskname').val('');
	$('#description').val('');
	$('#taskdatetimestart').val('');
	$('#taskdatetimeend').val('');
	$('#resources').val('');
	$('#dependencies').val('');
	$str = "";
	<?php
	$str = "";
	foreach ($statuses as $status) {
		foreach ($on_complete_opts as $id=>$name) {
			//echo " $(\"#oncomplete_".$status."_".$id."\").attr('false');\n";			
			echo "$('input:checkbox[name=oncomplete_".$status."_".$id."]').attr('checked',false);";
		}
	}	
	?>
}
function ShowTasks() {
	 $("#task_home").hide();
	 $('#task_ajax').fadeIn('slow');
}
function ShowEvents() {
	 $('#events').fadeIn('slow');
}
$(function() {
    $( "#eventdatetimestart, #eventdatetimeend, #taskdatetimestart, #taskdatetimeend" ).datetimepicker({
    	dateFormat: 'yy-mm-dd',
		duration: '',
        showTime: true,
        constrainInput: false,
		time24h: true
     });
});
function RefreshEvents() {
	$("#loading").show();
	$("#post_message").hide();
	$('#events').fadeIn('slow');
	$("#event_more").hide();
	$("#task_home").show();	
	$("#task_buttons").hide();	
	$("#task_create_form").hide();	
	$("#task_ajax").hide();	
	$("#add_resource_form").hide();
	$("#task_more").empty();
	$("#debug").empty();
	
	var div = "#events";
	$.ajax({
	  url: "ajax/events.php",
	  cache: false,
	  success: function(html){		
		$(div).html(html);
	  }
	});
	$("#loading").hide();
}
function DoAjax(event,div) {
	$("#loading").show();
	var div = "#"+div;
	$.ajax({
	  url: "ajax/"+event+".php",
	  cache: false,
	  success: function(html){		
		$(div).html(html);
	  }
	});
	$("#loading").hide();
}
function AjaxCall(event,data,div) {
	$("#loading").show();
	
	var div = "#"+div;
	$.ajax({
	  url: "ajax/"+event+".php",
	  data: data,
	  cache: false,
	  success: function(html){
		$(div).html(html);
	  }
	});
	$("#loading").hide();
}
DoAjax("events","events");
function AddResource() {
	$('#button_add_resource').attr("disabled", true);

	$.post("ajax/add_resource.php", { 
		eventid: global_eventid,
		name: document.getElementById('form_resource_name').value,
		email_address: document.getElementById('form_resource_email_address').value,
		contact: document.getElementById('form_resource_contact').value,
		roleid: $("input[name='userrole']:checked").val()
		},		
	   function(data) {		
		
		alert(data);
		$('#button_add_resource').attr("disabled", false);
		
	   });
		//$('#create_event_form').fadeOut('slow');
		//$('#events').fadeIn('slow');
		//DoAjax("events","events");
		
	   return false;
}
function AddEvent() {
	$("#loading").show();
	$.post("ajax/add_event.php", { 
		eventid: document.getElementById('eventid').value,
		eventname: document.getElementById('eventname').value,
		datetimestart: document.getElementById('eventdatetimestart').value,  
		datetimeend: document.getElementById('eventdatetimeend').value,
		locked: $("#locked").attr('checked')
		},
	   function(data) {
		 alert(data);
	   });
		//$('#create_event_form').fadeOut('slow');
		//$('#events').fadeIn('slow');
		//DoAjax("events","events");
	$("#loading").hide();
	return false;
}
function AddTask(action) {	
	$.post("ajax/add_task.php", { 
		taskid: document.getElementById('taskid').value,
		taskname: document.getElementById('taskname').value, 
		description: document.getElementById('description').value, 
		taskdatetimestart: document.getElementById('taskdatetimestart').value,  
		eventid: global_eventid,
		taskdatetimeend: document.getElementById('taskdatetimeend').value,
		resources: document.getElementById('resourceid').value,
		dependencies: document.getElementById('dependencyid').value,
		<?php
		$str = "";
		foreach ($statuses as $status) {
			foreach ($on_complete_opts as $id=>$name) {
				$str .= "oncomplete_".$status."_".$id.": $(\"#oncomplete_".$status."_".$id."\").attr('checked') ,\n";
				//echo "alert(document.getElementById('oncomplete_".$status."_".$id."').is(':checked'));";
			}
		}
		echo substr($str,0,-2);
		?>
		},
	   function(data) {		
		 alert(data);
	 });	
	 if (action == "showtasks") {
		//CancelNewTask();
	 }
	 if (action == "resetform") {
		ResetTaskForm();
	 }
	 PopulateDropDown("json_dependencies","dependencyid","eventid="+global_eventid);
	return false;
}
function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
function ResourcesAutoComplete() {	
	$( "#resources" )
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: "ajax/json_users.php?eventid="+global_eventid,
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		});
}
function DependenciesAutoComplete() {	
	$( "#dependencies" )
		// don't navigate away from the field on tab when selecting an item
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source: "ajax/json_event_tasks.php?eventid="+global_eventid,
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		});
}

function AjaxLoadTasks(eventid,type) {
	global_eventid = eventid;
	$("#eventid").val(eventid);
	
	$("#loading").show();
	$("#col1").show()
	$('#task_content').fadeIn('slow');
	$('#task_buttons').fadeIn('slow');
	$('#task_home').hide();
	$('#upload_file').hide();
	
	var div = "#task_ajax";
	var url = "ajax/tasks.php?eventid="+eventid+"&filter="+type;	
	$.ajax({
	  url: url,
	  cache: false,
	  dataType: "html",
	  success: function(html){		
		$(div).html(html);
	  }
	});
	AjaxLoadMessages(eventid);
		
	$("#task_ajax").show();
	$("#post_message").show();
	$("#loading").hide();
}
function AjaxTaskStatusUpdate(ptaskid,pstatus) {		
	$("#loading").show();
	
	var jqxhr = $.post("ajax/task_status_update.php", { 
		taskid: ptaskid, 
		status: pstatus  		
		},
	   function(data) {
		 alert(data);
		})	
    .success(function() { 
		$("#loading").hide(); 
		AjaxLoadTasks(global_eventid,''); 
	}
	);		
	return false;
}

function AjaxDeleteTask(ptaskid) {		
	var url = "ajax/delete_task.php?taskid="+ptaskid;
	$.ajax({
	  url: url,
	  cache: false,
	  success: function(html){		
		alert(html);
	  }
	});	
	AjaxLoadTasks(global_eventid,'');
}
function AjaxLoadMessages(eventid) {
	var url = "ajax/userevent_browse_user_details.php?eventid="+eventid;
	//alert(url);
	var role = ajaxDataRenderer(url);
	//role = eval(role);
	//alert(role);
	
	var div = "#messages";
	if (role == "Management") {
		//alert('mgmnt');
		$("#event_more_buttons").empty();		
		$("#col3").empty();
		$("#task_buttons").empty();
		//$("#event_buttons").empty();
		ShowChart('event_progress','col3')
		//ShowOdoChart('col3');
	}
	else {
		//alert('not mgmnt');
		var url = "ajax/messages.php?eventid="+eventid+"&taskid="+global_taskid;
		$.ajax({
		  url: url,
		  cache: false,
		  success: function(html){		
			$(div).html(html);
		  }
		});
	}
}
function AjaxSubmitMessage() {		
	var msg = $("#message").val();
	//$("#message").empty();
	document.getElementById('message').value = '';
	$.ajax({
		type: "POST",
		url: 'ajax/add_message.php',
		data : 'message=' + msg + '&eventid=' + global_eventid + '&taskid=' + global_taskid,
		success: function(msg) {
			//alert(msg);
			AjaxLoadMessages(global_eventid);
			return false;
		}
	});		
	return false;
}
function EventToggle(id,total,toggle) {
	var hidden, collapse;
	global_event_count = total;
	if (toggle) {
		hidden   = 'hidden';
		collapse = 'collapse';
	}
	else {
		hidden   = 'visible';
		collapse = 'visible';
	}
	//alert('Showing:'+id+' out of '+total);
	for (var i=0;i<total;i++) {
		//alert('Looping:'+i);
		if (id!=i) {
			//alert('Setting to yellow:'+i);		
			document.getElementById(i).style.visibility=hidden;
			document.getElementById(i).style.visibility=collapse;
		}		
	}
}
function EventHighlight(id,total) {
	//alert('Showing:'+id+' out of '+total);
	for (var i=0;i<total;i++) {
		//alert('Looping:'+i);
		if (id==i) {
			//alert('Setting to yellow:'+i);		
			document.getElementById(i).style.backgroundColor='yellow';
		}
		else {
			//alert('Un-Setting to blank:'+i);		
			document.getElementById(i).style.backgroundColor='';
		}
	}
}
function EventMore() {		
	$("#event_more").show();
}
function EditEvent() {
	$("#loading").show();
	CreateNewEvent();	
	var jqxhr = $.getJSON('ajax/json_edit_event.php?eventid='+global_eventid, function(data){		
		$.each(data, function(i,item) {		  
			$("#eventid").val(global_eventid);
			$("#eventname").val(item.eventname);
			$("#eventdatetimestart").val(item.datetimestart);
			$("#eventdatetimeend").val(item.datetimeend);
			if (item.locked == "y") {
				//alert("OK");
				$("#locked").prop("checked", true);
			}
		});
    }	
	);
	
}
function EditTask(taskid) {
	CreateNewTask();
	ResetTaskForm();
	$.getJSON('ajax/json_edit_task.php?taskid='+taskid, function(data){		
		$.each(data, function(i,item){		  
		  $("#taskid").val(taskid);
		  $("#taskname").val(item.taskname);
		  $("#description").val(item.description);
		  $("#taskdatetimestart").val(item.datetimereqstart);
		  $("#taskdatetimeend").val(item.datetimereqend);
		  $("#resourceid").val(item.resources);
		  $("#dependencyid").val(item.dependencies);
		  $.each(item.inprogress, function(i,item1){				
			$("#oncomplete_inprogress_"+item1).prop("checked", true);
		  });
		  $.each(item.complete, function(i,item1){				
			$("#oncomplete_complete_"+item1).prop("checked", true);
		  });
		  $.each(item.issues, function(i,item1){				
			$("#oncomplete_issues_"+item1).prop("checked", true);
		  });
		});				
    });
}
function DeleteEvent() {	
	var url = "ajax/event_delete.php?eventid="+global_eventid;
	$.ajax({
	  url: url,
	  cache: false,
	  success: function(html){		
		alert(html);
	  }
	});	
	DoAjax('events','events');
}
function ClearDiv(divid) {
	$("#"+divid).empty();
}
function ajaxDataRenderer(url) {
		//alert(url);
		var ret = null;
		
		$.ajax({
			async: false,
		  type: "GET",
		  url: url
		}).done(function( msg ) {
		  //alert( "Data Saved: " + msg );
		  ret = msg;
		});
		return ret;
}
function ShowChart(chart,div) {
	
	$('#task_ajax').hide();
	$('#'+div).empty();
	$('#task_ajax').hide();	
 
  // The url for our json data
  var jsonurl = "./jsondata1.txt";

	var line1 = ajaxDataRenderer("ajax/json_jqplot_event_vals.php?q=1&eventid="+global_eventid);	
	line1 = eval(line1);
	
	var line2 = ajaxDataRenderer("ajax/json_jqplot_event_vals.php?q=2&eventid="+global_eventid);
	line2 = eval(line2);
		
	var plot2 = $.jqplot(div, [line1, line2], {
      axes: {
        xaxis: {
          renderer: $.jqplot.CategoryAxisRenderer,
          label: 'Tasks',
          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
          tickOptions: {
              // labelPosition: 'middle',
              angle: 15
          }
           
        },
        yaxis: {          
			label: 'Cumulative Hours',
			labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        }
      }
    });	
}
function ShowOdoChart(div) {

	$('#task_ajax').hide();
	$('#'+div).empty();
	$('#task_ajax').hide();
	
	//s1 = [92];
	var url = "ajax/jqplot_odometer.php?eventid="+global_eventid;
	//alert(url);
	s1 = ajaxDataRenderer(url);
	s1 = "["+s1+"]";
	s1 = eval(s1);
	//alert(s1);
   plot3 = $.jqplot(div,[s1],{
       seriesDefaults: {
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
               min: 0,
               max: 100,
               intervals:[20, 50, 90, 100],
			   
               intervalColors:['green', 'yellow', 'red', 'black'],
			   label: 'Event Tracking as Percentage from Original',
               labelPosition: 'bottom',
               labelHeightAdjust: -5,
           }
       }
   });
}
function AjaxUploadFile() {
	$('#add_resource_form').hide();
	$('#task_create_form').hide();
	$('#task_ajax').hide();
	$('#tasks_more').hide();
	$('#upload_file').show();
}
function UploadFile() {
	var options = { 
		type: "POST",
		target:     '#uploadDiv', 
		url:        'ajax/upload_document.php?eventid='+global_eventid,
		iframe: true,
		success:    function(data) { 
			alert(data);
		},
	};
	// bind 'uploadForm' and provide a simple callback function 
	$('#uploadForm').ajaxSubmit(options);
	
	return false;
}

function PopulateDropDown(ajax,div,data) {
	//alert(ajax+' '+div+' '+data);
	$.ajax({
	  url: "ajax/"+ajax+".php",	  
	  data: data,
	  cache: false,
	  //async: false,
	  dataType: "json",
	  success: function(html){
		//alert(html);
		//jsonList = html;
		var listItems= "<option value=''>--==Select==--</option>";
		$.each(html, function(i, item){			
			listItems+= "<option value='" + item.id + "'>" + item.value + "</option>";
		});
		$("#"+div).html(listItems);
	  },
	  error: function() {		
		alert('Error occured'+ajax+' div='+div);
	  }
	}); 
}
</script>
</head>
<body>
<div style="width:100%;height:40px;background-color:#e6e6e6">
	<div style="float:left;width:80%"><img src="images/logo.gif" height="40"></div>	
	<div style="background-color:white;float:right;width:20%;height:40px">
		<span style="float:left;width:150px"><a href='console.php?content=account'>+ My Account</a> | <a href='logout.php'>Sign Out</a></span>
		<span id="loading" style="float:right;width:50px"><img src="images/loading.gif"></span>
	</div>	
</div>
<div class="colmask threecol">
	<div class="colmid">
		<div class="colleft">
			<div class="col1" id="col1">
				<div id="task_content">
					<div id='debug'></div>
					<div id="task_home">
						<table style='padding:25px;text-align:center' cellpadding=30>
							<tr>
								<td colspan=4 class=header>Create a new event</td>
							</tr>
							<tr>
								<td><a href="#" onClick="CreateNewEvent()"><img border='0' src="images/crystalclear/48x48/apps/edit.png"><br />Create Event</a></td>
								<td><a href="#" onClick="ToggleDiv('add_resource_form','show')"><img border='0' src="images/crystalclear/48x48/apps/personal.png"><br />Add User</a></td>
								<td><a href="console.php?content=orgusers"><img border='0' src="images/crystalclear/48x48/actions/agt_family.png"><br />Org Users</a></td>
								<td><a href="index.php?content=bugs"><img border='0' src="images/crystalclear/48x48/apps/bug.png"><br />Report a bug</a></td>
							</tr>
							<tr>
								<td><a href="wiki/"><img border='0' src="images/crystalclear/48x48/apps/miscellaneous2.png"><br />Get Help</a></td>
								<td><a href="console.php?content=account"><img border='0' src="images/crystalclear/48x48/apps/password.png"><br />My Account</a></td>
								<td><a href="index.php?content=contact"><img border='0' src="images/crystalclear/48x48/apps/messenger.png"><br />Contact Us</a></td>
								<td><a href="logout.php"><img border='0' src="images/crystalclear/48x48/apps/exit.png"><br />Signout</a></td>
							</tr>
						</table>
					</div>
					<div id="task_buttons">
						<button class="ui-state-default ui-corner-all" id="create-task" onClick="CreateNewTask()">Create New Task</button>
						<button class="ui-state-default ui-corner-all" id="create-task" onClick="AjaxLoadTasks(global_eventid,'my')">My Tasks</button>
						<button class="ui-state-default ui-corner-all" id="tasks_completed" onClick="AjaxLoadTasks(global_eventid,'pending')">Pending Tasks</button>
						<button class="ui-state-default ui-corner-all" id="tasks_completed" onClick="AjaxLoadTasks(global_eventid,'complete')">Completed Tasks</button>
						<button class="ui-state-default ui-corner-all" id="tasks_completed" onClick="AjaxLoadTasks(global_eventid,'issues')">Tasks with Issues</button>
						<button class="ui-state-default ui-corner-all" id="tasks_all" onClick="AjaxLoadTasks(global_eventid,'')">All Tasks</button>
						<button class="ui-state-default ui-corner-all" id="tasks_all" onClick="AjaxUploadFile()">Upload File</button>
					</div>
					<div id='task_create_form' class="demo">
						<div id="dialog-task-form" title="Create new tasks">
							<p class="validateTips">All form fields are required.</p>						
							<fieldset>
								<input type="hidden" id="taskid" value="" />
								<label for="taskname">Task</label>
								<input type="text" name="taskname" id="taskname" class="text ui-widget-content ui-corner-all" />
								<label for="description">Description</label>
								<input type="text" name="description" id="description" value="" class="text ui-widget-content ui-corner-all" />
																
								<label for="resources">Resource</label><br />
								<select id="resourceid" name="resourceid"></select><br />
								<br />
								<label for="dependency">Dependency</label><br />
								<select id="dependencyid" name="dependencyid"></select><br />
								<br />
								<label for="taskdatetimestart">Start</label>
								<input type="text" name="taskdatetimestart" id="taskdatetimestart" value="" class="text ui-widget-content ui-corner-all" />
								<label for="taskdatetimeend">End</label>
								<input type="text" name="taskdatetimeend" id="taskdatetimeend" value="" class="text ui-widget-content ui-corner-all" />
								
								<?php							
								foreach ($statuses as $status) {
									echo "<b>When task changes status to: $status</b><br />\n";
									//echo "<div style='width:100%'><nobr>\n";
									$str = "";
									foreach ($on_complete_opts as $id=>$name) {
										$str .= "<input type='checkbox' value='yes' id='oncomplete_".$status."_".$id."' name='oncomplete_".$status."_".$id."' /> $name  | ";
									}
									$str = substr($str,0,-2);
									echo "$str <br /><br />\n";
								}
								?>								
								<input onClick="return AddTask()" type="button" value="Save & Stay" class="ui-state-default ui-corner-all" id="add_task"/>
								<input onClick="return ResetTaskForm()" type="button" value="Clear Form" class="ui-state-default ui-corner-all" id="add_task_reset"/>
								<input onClick="ToggleDiv('task_create_form','');ToggleDiv('task_more','');ToggleDiv('tasks_ajax','show');AjaxLoadTasks(global_eventid,'')" type="button" value="Back to Show Tasks" class="ui-state-default ui-corner-all" />
							</fieldset>						
						</div>					
					</div>
					<div id='task_ajax'>Loading Tasks</div>
				</div>
				<div id='add_resource_form' class="demo">
					<form onSubmit="return AddResource()">
						<fieldset>
							<label for="name">Name</label>
							<input type="text" name="form_resource_name" id="form_resource_name" class="text ui-widget-content ui-corner-all" />
							<label for="name">Email</label>
							<input type="text" name="form_resource_email_address" id="form_resource_email_address" class="text ui-widget-content ui-corner-all" />
							<label for="name">Contact #</label>
							<input type="text" name="form_resource_contact" id="form_resource_contact" class="text ui-widget-content ui-corner-all" />
							<label for="role_admin">Admin</label>
							<input type='radio' name='userrole' value='1' id='role_admin' />
							<label for="role_user">User</label>
							<input type='radio' name='userrole' value='2' id='role_user' />
							<label for="role_management">Managent</label>
							<input type='radio' name='userrole' value='3' id='role_management' />
							<br />
							<input type="submit" value="Add" class="ui-state-default ui-corner-all" id="button_add_resource" />
							<input onClick="ToggleDiv('add_resource_form','');ToggleDiv('tasks','show')" type="button" value="Back" class="ui-state-default ui-corner-all" id="cancel_new_event"/>
						</fieldset>
					</form>
				</div>
				<div id='upload_file'>
					<form id="uploadForm" onSubmit="return UploadFile()" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
						<fieldset>
							<label for="name">File</label>
							<input type="file" name="userfile" id="userfile" class="text ui-widget-content ui-corner-all" />							
							<input type="submit" value="Upload" class="ui-state-default ui-corner-all" id="add_resource" />
							<label>Output:</label>							
						</fieldset>						
					</form>
					<div id="uploadDiv"></div>					
				</div>
				<div id='task_more'></div>
			</div>
			<div class="col2" id="col2">
				<div id="event_buttons">
					<button class="ui-state-default ui-corner-all" id="create-task" onClick="CreateNewEvent()">Create New Event</button>
					<button class="ui-state-default ui-corner-all" id="create-task" onClick="RefreshEvents();">Refresh Events</button>
				</div>
				<div id="create_event_form" title="Create new event">
					<p class="validateTips">All form fields are required.</p>					
					<fieldset>
						<input type="hidden" id="eventid" value="" />
						<label for="name">Event Name</label>
						<input type="text" name="eventname" id="eventname" class="text ui-widget-content ui-corner-all" />
						<label for="email">Date From</label>
						<input type="text" name="eventdatetimestart" id="eventdatetimestart" value="" class="text ui-widget-content ui-corner-all" />
						<label for="password">Date To</label>
						<input type="text" name="eventdatetimeend" id="eventdatetimeend" value="" class="text ui-widget-content ui-corner-all" />
						<label for="lockedevent">Lock tasks from changes?</label>
						<input type='checkbox' value='y' id='locked' name='locked' />
						<br />
						<input type="button" value="Save" class="ui-state-default ui-corner-all" id="add_event" onClick="AddEvent()" />
						<input type="button" value="Back" class="ui-state-default ui-corner-all" id="cancel_new_event" onClick="CancelNewEvent()" />
					</fieldset>					
				</div>							
				
				<br />
				<div id='events'>Loading Events...</div>
				<div id='event_more'>
					<div id='event_more_buttons'>
						<input type='button' value='Event Users' class='ui-state-default ui-corner-all' id='event_users' onClick="AjaxCall('event_users','eventid='+global_eventid,'task_ajax')" /> <br />
						<input type='button' value='Add People' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick="ToggleDiv('add_resource_form','show')" /> <br />
						<input type='button' value='Edit Event' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick='EditEvent()' /><br />
						<input type='button' value='Clone Event' class='ui-state-default ui-corner-all' id='clone_new_event' onClick="AjaxCall('event_clone','eventid='+global_eventid,'debug')" /><br />
						<input type='button' value='Delete Event' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick='DeleteEvent()' /><br />
					</div>
					<h1>Charts</h1>
					<input type='button' value='Actual versus Estimates' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick="ShowChart('event_progress','task_more')" />
					<br />
					<input type='button' value='Odometer Tracking' class='ui-state-default ui-corner-all' id='cancel_new_event' onClick="ShowOdoChart('task_more')" />
				</div>
			</div>
			<div class="col3" id="col3">				
				<form id='post_message' onSubmit="return AjaxSubmitMessage()">
				<fieldset>
					<label for="name">Enter Message:</label>
					<input type="text" name="message" id="message" value='Say Something...' onClick="this.value=''" class="text ui-widget-content ui-corner-all" />
				</form>
				<div id='messages' style="height: 700px; width: 325px; overflow: auto;">Loading Messages...</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>