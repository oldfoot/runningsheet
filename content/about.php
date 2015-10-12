<?php
class about {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		
		$arr_key_features = array(
							"info.png"=>"<strong>Contact us via email</strong> - general [at] runningsheet.com (replace [at] with @)",
							"run.png"=>"<strong>Developer API</strong> - Find out more about our developer tools.",
							"lock.png"=>"<strong>Privacy is important</strong> - we never share information about our customers.",
							"mobile.png"=>"<strong>Go Mobile</strong> - access the site from most mobile devices."
							);
		$key_features = "";
		foreach ($arr_key_features as $key=>$val) {		
			$key_features .= "<div class='wrapper pad_bot1'>
								<figure class='left marg_right1'><a href='#'><img src='images/crystalclear/64x64/actions/$key' alt=''></a></figure>
								<p>$val</p>								
							</div>
			";
			
		}
		
		$this->html .= "
			<section id='content'>
				<div class='wrapper'>
					<div class='pad'>
						<div class='wrapper'>
							<article class='col1'><h2>About</h2></article>							
						</div>
					</div>										
					<article class='col2 pad_left1' style='width:90%'>
						<div class='wrapper'>
							$key_features
						</div>
					</article>					
				</div>
			</section>
			";
		return $this->html;
	}	
}
?>