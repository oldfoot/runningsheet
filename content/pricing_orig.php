<?php
class pricing {

	public function __construct() {
		$this->html = "";
	}	
	
	public function HTML() {
		
		$table = "";
				
		$sql = "SELECT *
				FROM admin_features
				ORDER BY Feature,find_in_set(Category, 'Free,Select,Advanced,Professional')";
		$result = $GLOBALS['db']->Query($sql);
		$count = 0;
		$feature = "whatever";
		$table .= "<br /><br /><table width:100%>\n";
			$table .= "<colgroup>  
						  <col class='vzebra-odd'>
						  <col class='vzebra-even'>
						  <col class='vzebra-odd'>
						  <col class='vzebra-even'>
						  <col class='vzebra-odd'>						  
					   </colgroup>";						
			$table .= "<thead><tr style='font-size:large'>\n";
			$table .= "<th width=40% scope='col' id='vzebra-comedy'>Feature</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>Free</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>Select</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>Advanced</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>Professional</th>\n";
			$table .= "</tr></thead>\n";
		while ($row = $GLOBALS['db']->FetchArray($result)) {			
			//echo "Loop starts<br />";
			//echo "Feature: $feature<br />";			
			if ($count == 0) {
				//echo "Count is zero<br />";
				$table .= "<tr>\n";
				$feature = $row['Feature'];
				$table .= "<td style='font-weight:bold'>".$row['Feature']." <a href='".$GLOBALS['wb']."wiki/doku.php?id=".strtolower($row['Feature'])."'>[?]</a></td>\n";
			}		
			
			//echo "Draw row<br />";
			
			$table .= "<td align='center'>".$row['FeatureValue']."</td>\n";
			
			$count++;
			if ($count == 4) {				
				//echo "var Feature not equal to row<br />";
				$table .= "</tr>\n";				
				$count = 0;
			}		
			
			//echo "End of loop<br />";
			$feature = $row['Feature'];
		}
		
			$table .= "<td colspan='5'><hr></td>\n";
		$sql = "SELECT *
				FROM admin_pricing
				ORDER BY find_in_set(Type, 'Monthly Payment,Annual Payment,Minimum Subscription,You Save'),
				find_in_set(Category, 'Free,Select,Advanced,Professional')";
		$result = $GLOBALS['db']->Query($sql);
		$count = 0;
		while ($row = $GLOBALS['db']->FetchArray($result)) {	
			if ($count == 0) {				
				$table .= "<tr>\n";				
				$table .= "<td style='font-weight:bold'>".$row['Type']."</td>\n";
			}			
			if ($row['Type'] != "Minimum Subscription") { $price = "$".$row['Price']; } else { $price = $row['Price']; }
			$table .= "<td align='center' style='font-size:large'>".$price."</td>\n";			
			$count++;
			if ($count == 4) {								
				$table .= "</tr>\n";				
				$count = 0;
			}
		}
			$table .= "<td colspan='5'><hr></td>\n";
			$table .= "<thead><tr style='font-size:large'>\n";
			$table .= "<th width=40% scope='col' id='vzebra-comedy'></th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'><a href='index.php?content=signup'>CHOOSE</a></th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>".PayPalPay(19,12,"M")."</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>".PayPalPay(39,6,"M")."</th>\n";
			$table .= "<th width=15% scope='col' id='vzebra-comedy'>".PayPalPay(49,12,"M")."</th>\n";
			$table .= "</tr></thead>\n";
			$table .= "<td colspan='5'><hr></td>\n";
		$table .= "</table>\n";
		
		return $table;
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
								$table
							</article>
							<article class='col2 pad_left1'>
								<div class='wrapper'>
									Col2
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
function PayPalPay($a3,$p3,$t3) {
	return "
		<form name='_xclick' action='https://www.paypal.com/cgi-bin/webscr' method='post'>
		<input type='hidden' name='cmd' value='_xclick-subscriptions'>
		<input type='hidden' name='business' value='me@mybusiness.com'>
		<input type='hidden' name='currency_code' value='USD'>
		<input type='hidden' name='no_shipping' value='1'>
		<input type='image' src='http://www.paypal.com/en_US/i/btn/btn_subscribe_LG.gif' border='0' name='submit' alt='Make payment with PayPal'>
		<input type='hidden' name='a3' value='$a3.00'>
		<input type='hidden' name='p3' value='$p3'>
		<input type='hidden' name='t3' value='$t3'>
		<input type='hidden' name='src' value='1'>
		<input type='hidden' name='sra' value='1'>
	</form>
	";
}
?>