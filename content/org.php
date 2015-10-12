<?php
class org {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		
		$c = "<script language='javascript'>
				function UploadLogo() {				
				var options = { 
					type: 'POST',
					target:     '#uploadDiv', 
					url:        'ajax/upload_logo.php',
					iframe: true,
					success:    function(data) { 
						alert('ok');
						alert(data);
					},
					error: function(data) {
						alert('error');
						alert(data);
					}
				};
				// bind 'uploadForm' and provide a simple callback function 
				$('#uploadForm').ajaxSubmit(options);				
				alert('Completed. Refresh the page to view results');
				return false;
			}			
			</script>
				<div class='pad'>
					<div class='wrapper'>
						<article class='col1'><h2>Corporate Logo</h2></article>
					</div>
				</div>	
				<form id='uploadForm' onSubmit='return UploadLogo()' method='POST' enctype='multipart/form-data'>
					<input type='hidden' name='MAX_FILE_SIZE' value='100000' />
						<fieldset>
							<label for='name'>Logo</label>
							<input type='file' name='userfile' id='userfile' />
							<input type='submit' value='Upload' id='add_resource' />
							<label>Output:</label>							
						</fieldset>						
				</form>
					
					";
		return $c;
	}	
}
?>