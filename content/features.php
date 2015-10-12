<?php
class features {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		
		$arr_key_features = array(
							"apps/internet.png"=>"Cloud solution - no hardware or software investment.",
							"apps/xfmail.png"=>"Email reminders - get reminders on task changes.",
							"apps/lists.png"=>"Maintain lists of tasks - easily copy and create new events.",							
							"actions/run.png"=>"<strong>Developer API</strong> - Find out more about our developer tools [coming soon]",							
							"actions/mobile.png"=>"<strong>Go Mobile</strong> - access the site from most mobile devices."
							);
		$key_features = "";
		foreach ($arr_key_features as $key=>$val) {		
			$key_features .= "<div class='wrapper pad_bot1'>
								<figure class='left marg_right1'><a href='#'><img src='images/crystalclear/64x64/$key' alt=''></a></figure>
								<p>$val</p>								
							</div>
			";
			
		}
		
		$this->html .= "
			<section id='content'>
				<div class='wrapper'>
					<div class='pad'>
						<div class='wrapper'>
							<article class='col1'><h2>Core Features</h2></article>
							<article class='col2 pad_left1'><h2>Key features</h2></article>
						</div>
					</div>
					<div class='box pad_bot1'>
						<div class='pad marg_top'>
							<article class='col1'>
								<div class='wrapper'>
									<figure class='left marg_right2'><img src='images/page3_img1.jpg' alt=''></figure>
									<p class='pad_bot3'><strong>Cross team task scheduling and timing management</strong></p>
									<p>With runningsheet.com you can assign tasks to team members that need to be executed at specific times, with specific dependencies and actions to be taken once the task is completed.</p>
								</div>
								<p class='pad_bot3'><strong>Management view of event status</strong></p>
								<p>Event coordinators can spend more time managing the event on the ground and get specific alerts when others have completed tasks. Get realtime information about whether an event is running ahead or behind schedule.</p>
								<p class='pad_bot3'><strong>Multiple events</strong></p>
								<p>Schedule multiple events at one time, receive email updates, mobile status updates and more.</p>
							</article>
							<article class='col2 pad_left1'>
								<div class='wrapper'>
									$key_features
								</div>
							</article>
						</div>
					</div>					
				</div>
			</section>
			";
		return $this->html;
	}	
}
?>