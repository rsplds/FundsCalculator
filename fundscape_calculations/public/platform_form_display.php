<?php
/**
 *  Shortcode Display Fundscape form on Front-End.
 *
 */
function fundscape_form_function() {

	$fundscape_form = '<div class="fundscape-form-section">';
	$fundscape_form .= '<form name="fundscape_form" id="fundscape-form" method="post">';
	$fundscape_form .= '<h4>Product Split of total investment (Fund) :</h4>';
	$fundscape_form .= '<div class="fundscape-fields-section"><label>GIA</label><input type="number" name="funds_gia" id="funds_gia" class="fundscape-field" min="0" /></div>';
	$fundscape_form .= '<div class="fundscape-fields-section"><label>ISA</label><input type="number" name="funds_isa" id="funds_isa" class="fundscape-field" min="0" /></div>';
	$fundscape_form .= '<h4>Product split of exchange-traded</h4>';
	$fundscape_form .= '<div class="fundscape-fields-section"><label>GIA</label><input type="number" name="exchange_trade_gia" id="exchange_trade_gia" class="fundscape-field" min="0" /></div>';
	$fundscape_form .= '<div class="fundscape-fields-section"><label>ISA</label><input type="number" name="exchange_trade_isa" id="exchange_trade_isa" class="fundscape-field" min="0" /></div>';
	$fundscape_form .= '<div class="fundscape-fields-section"><input type="button" class="fundscape-btn" id="fundscape-submit" value="Calculate"></div>';
	$fundscape_form .= '</form>';
	$fundscape_form .= '<div id="show-form-result"></div>';
	$fundscape_form .= '</div>';

	return $fundscape_form;

}
add_shortcode( 'fundscape_form', 'fundscape_form_function' );


/**
 *  Ajax Function to Calculate Fund
 *
 */
function calculate_fund() {

	global $wpdb;
	$success = false;
	$result = array(
					"gia_fund_charge" => 0.00,
					"gia_charges_et" => 0.00,
					"isa_charges_fund" => 0.00,
					"isa_charges_et" => 0.00,
					"total_charges" => 0.00
				);

	$platform=array();

	$gia_fund = isset($_POST[ 'funds_gia' ]) ? $_POST[ 'funds_gia' ] : 0.00;
	$isa_fund = isset($_POST[ 'funds_isa' ]) ? $_POST[ 'funds_isa' ] : 0.00;
	$gia_et = isset($_POST[ 'exchange_trade_gia' ]) ? $_POST[ 'exchange_trade_gia' ] : 0.00;
	$isa_et = isset($_POST[ 'exchange_trade_isa' ]) ? $_POST[ 'exchange_trade_isa' ] : 0.00;

	$total_investment = $gia_fund + $gia_et + $isa_fund + $isa_et;

	$tiering_methods = array(
							"1" => "One rate based on total assets",
							"2" => "Combined tiered rate (Split)",
							"3" => "Combined tiered rate (Not split)",
							"4" => "Combined tiered rate using total assets across all products (Split)",
							"5" => "Combined tiered rate using total assets across all products (Not split)",
						);

	if ( !empty( $gia_fund ) || !empty( $isa_fund ) ) {

		$platform_info = 'There is some problem in calculation. Please try again';
		$active_platform = stripslashes( get_option( 'active_platform' ) );

		if ( !empty( $active_platform ) && $active_platform !== 'none' ) {

			$success = true;

			$table_name = $wpdb->prefix . 'fundscape_management';
			$platform_data = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE platform_id='" . $active_platform . "'" );

			$platform_funds = json_decode( $platform_data[0]->platform_funds, true );
			$platform_et = json_decode( $platform_data[0]->platform_exchange_trades, true );

			$fund_count = 1;
			foreach ( $platform_funds as $key => $value ) {
				$platform['fund']['tier'.$fund_count] = $value;
				$fund_count++;
			}

			$et_count = 1;
			foreach ( $platform_et as $key => $value ) {
				$platform['et']['tier'.$et_count] = $value;
				$et_count++;
			}

			$selected_method = $platform_data[0]->platform_method;
			switch ( $selected_method ) {
				case 1:
					$result =  tiering_method_first( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform );
					break;
				case 2:
					$result =  tiering_method_secound( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform );
					break;
				case 3:
					$result =  tiering_method_third( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform );
					break;
				case 4:
					$result =  tiering_method_four( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform );
					break;
				case 5:
					$result =  tiering_method_fifth( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform );
					break;
				default:
					break;
			}

		}

	}

    echo json_encode( array( 'result' => $result, 'success' => $success ) );
    exit();
}

add_action('wp_ajax_calculate_fund', 'calculate_fund');
add_action('wp_ajax_nopriv_calculate_fund', 'calculate_fund');


/**
 * Method 1: One rate based on total assets
 * When method 1 applies it is always the case that the percentages for each tier are the same across all products
 *
 */
function tiering_method_first( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform ) {

	$rate_charges=0.00; // percentage rate of charges

	foreach ($platform['fund'] as $key => $value) {
		if( ($value['bandsfrom'] <= $total_investment) && ($value['bandsto'] >= $total_investment) ){
			$rate_charges=$value['gia'];
			break;
		}
	}


	// Calculation of gia and isa charges based on percentage rate
	$gia_charges_fund = 0;
	$gia_charges_et = 0;

	$isa_charges_fund = 0;
	$isa_charges_et = 0;

	$gia_charges_fund = getPercentageValue($gia_fund,$rate_charges);
	$gia_charges_fund = number_format((float)$gia_charges_fund, 2, '.', '');

	$gia_charges_et = getPercentageValue($gia_et,$rate_charges);
	$gia_charges_et = number_format((float)$gia_charges_et, 2, '.', '');

	$isa_charges_fund = getPercentageValue($isa_fund,$rate_charges);
	$isa_charges_fund = number_format((float)$isa_charges_fund, 2, '.', '');

	$isa_charges_et = getPercentageValue($isa_et,$rate_charges);
	$isa_charges_et = number_format((float)$isa_charges_et, 2, '.', '');


	$total_charges = $gia_charges_fund+$gia_charges_et+$isa_charges_fund+$isa_charges_et;
	$total_charges = number_format((float)$total_charges, 2, '.', '');

	$result = array(	"gia_fund_charge" => $gia_charges_fund,
			"gia_charges_et" => $gia_charges_et,
			"isa_charges_fund" => $isa_charges_fund,
			"isa_charges_et" => $isa_charges_et,
			"total_charges" => $total_charges,
			"total_investment" => $total_investment
		);
	//print '<pre>';print_r($result);
	return  $result;

}


/**
 * Method 2: Combined tiered rate (Split)
 *
 */
function tiering_method_secound( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform ) {

	$rate_charges=0.00; // percentage rate of charges

	$gia_charges_fund = 0;
	$gia_charges_et = 0;

	$isa_charges_fund = 0;
	$isa_charges_et = 0;


	$gia_charges_fund =  getGIAFundChargesFromTier($gia_fund,$platform);
	$gia_charges_et   =  getGIAETChargesFromTier($gia_et,$platform);
	$isa_charges_fund =  getISAFundChargesFromTier($isa_fund,$platform);
	$isa_charges_et   =  getISAETChargesFromTier($isa_et,$platform);

	$total_charges = $gia_charges_fund+$gia_charges_et+$isa_charges_fund+$isa_charges_et;
	$total_charges = number_format((float)$total_charges, 2, '.', '');


	$result = array(	"gia_fund_charge" => $gia_charges_fund,
			"gia_charges_et" => $gia_charges_et,
			"isa_charges_fund" => $isa_charges_fund,
			"isa_charges_et" => $isa_charges_et,
			"total_charges" => $total_charges,
			"total_investment" => $total_investment
		);
	//print '<pre>';print_r($result);
	return  $result;

}

/**
 * Method 3: Combined tiered rate (Not split)
 *
 */
function tiering_method_third( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform ) {

	$rate_charges=0.00; // percentage rate of charges

	$gia_charges_fund = 0;
	$gia_charges_et = 0;

	$isa_charges_fund = 0;
	$isa_charges_et = 0;

		//echo $gia_fund."==".$gia_et;exit;
		$gia_overall_investment=$gia_fund+$gia_et;
	$gia_charges_fund =  getGIAFundChargesFromTier($gia_overall_investment,$platform);

	$gia_overall_charge=($gia_overall_investment != 0)?($gia_charges_fund/$gia_overall_investment)*100:0.00;

	//$gia_charges_et   =  getGIAETChargesFromTier($gia_et,$platform);
	$isa_overall_investment = $isa_fund+$isa_et;
	$isa_charges_fund =  getISAFundChargesFromTier($isa_overall_investment,$platform);

	$isa_overall_charge=($isa_overall_investment != 0)?($isa_charges_fund/$isa_overall_investment)*100:0.00;
	//$isa_charges_et   =  getISAETChargesFromTier($isa_et,$platform);



	$gia_overall_charge = number_format((float)$gia_overall_charge, 4, '.', '');
	$isa_overall_charge = number_format((float)$isa_overall_charge, 4, '.', '');


	$gia_charges_fund = getPercentageValue($gia_fund,$gia_overall_charge);
	$gia_charges_et   = getPercentageValue($gia_et,$gia_overall_charge);

	$isa_charges_fund = getPercentageValue($isa_fund,$isa_overall_charge);
	$isa_charges_et   = getPercentageValue($isa_et,$isa_overall_charge);

	$total_charges = $gia_charges_fund+$gia_charges_et+$isa_charges_fund+$isa_charges_et;
	$total_charges = number_format((float)$total_charges, 2, '.', '');

	$result = array(	"gia_fund_charge" => $gia_charges_fund,
			"gia_charges_et" => $gia_charges_et,
			"isa_charges_fund" => $isa_charges_fund,
			"isa_charges_et" => $isa_charges_et,
			"total_charges" => $total_charges,
			"total_investment" => $total_investment
		);
	//print '<pre>';print_r($result);
	return  $result;

}

/**
 * Method 4: Combined tiered rate using total assets across all products (Split)
 *
 */
function tiering_method_four( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform ) {

	$rate_charges=0.00; // percentage rate of charges

	$gia_charges_fund = 0;
	$gia_charges_et = 0;

	$isa_charges_fund = 0;
	$isa_charges_et = 0;


	$overall_investment_fund = $gia_fund+$isa_fund;
	$overall_investment_et = $gia_et+$isa_et;
	//echo $overall_investment_fund."==".$overall_investment_et;exit;
	$gia_charges_fund =  getGIAFundChargesFromTier($overall_investment_fund,$platform);
	//echo $gia_charges_fund;exit;
	$gia_fund_charge=($overall_investment_fund != 0)?($gia_charges_fund/$overall_investment_fund)*100:0.00;

	//echo $gia_fund_charge;exit;

	$gia_charges_et =  getGIAETChargesFromTier($overall_investment_et,$platform);

	$gia_et_charge=($overall_investment_et != 0)?($gia_charges_et/$overall_investment_et)*100:0.00;

	//echo $gia_fund_charge."====".$gia_et_charge;exit;


	$isa_charges_fund =  getISAFundChargesFromTier($overall_investment_fund,$platform);
	//echo $gia_charges_fund;exit;
	$isa_fund_charge=($overall_investment_fund != 0)?($isa_charges_fund/$overall_investment_fund)*100:0.00;

	//echo $gia_fund_charge;exit;

	$isa_charges_et =  getISAETChargesFromTier($overall_investment_et,$platform);

	$isa_et_charge=($overall_investment_et != 0)?($isa_charges_et/$overall_investment_et)*100:0.00;

	//echo $isa_fund_charge."====".$isa_et_charge;exit;



	//$gia_overall_charge = number_format((float)$gia_overall_charge, 4, '.', '');
	//$isa_overall_charge = number_format((float)$isa_overall_charge, 4, '.', '');


	$gia_charges_fund = getPercentageValue($gia_fund,$gia_fund_charge);
	$gia_charges_et   = getPercentageValue($gia_et,$gia_et_charge);

	$isa_charges_fund = getPercentageValue($isa_fund,$isa_fund_charge);
	$isa_charges_et   = getPercentageValue($isa_et,$isa_et_charge);

	$total_charges = $gia_charges_fund+$gia_charges_et+$isa_charges_fund+$isa_charges_et;
	$total_charges = number_format((float)$total_charges, 2, '.', '');

	$result = array(	"gia_fund_charge" => $gia_charges_fund,
			"gia_charges_et" => $gia_charges_et,
			"isa_charges_fund" => $isa_charges_fund,
			"isa_charges_et" => $isa_charges_et,
			"total_charges" => $total_charges,
			"total_investment" => $total_investment
		);
	//print '<pre>';print_r($result);
	return  $result;

}


/**
 * Method 5: Combined tiered rate using total assets across all products (Not split)
 * It is based on calculating a combined custody charge using the total assets invested by a client across all products (GIA, ISA and so on) without splitting funds and exchange-traded investments.
 * So if I had £250,000 (£50,000 in exchange-traded) invested in a GIA and £750,000 (£250,000 in exchange-traded) in a ISA my total investment across all products would be £1,000,000. This total investment value is then used to determine a combined custody charge percentage.
 *
 */
function tiering_method_fifth( $gia_fund, $gia_et, $isa_fund, $isa_et, $total_investment, $platform ) {

	$rate_charges=0.00; // percentage rate of charges

	$gia_charges_fund = 0;
	$gia_charges_et = 0;

	$isa_charges_fund = 0;
	$isa_charges_et = 0;


	//$overall_investment_fund = $gia_fund+$isa_fund;
	//$overall_investment_et = $gia_et+$isa_et;
	//echo $overall_investment_fund."==".$overall_investment_et;exit;
	$gia_charges_fund =  getGIAFundChargesFromTier($total_investment,$platform);
	$combined_custody_charge=($total_investment != 0)?($gia_charges_fund/$total_investment)*100:0.00;//combined custody charge

	//echo $gia_fund_charge;exit;


	$gia_charges_fund = getPercentageValue($gia_fund,$combined_custody_charge);
	$gia_charges_et   = getPercentageValue($gia_et,$combined_custody_charge);

	$isa_charges_fund = getPercentageValue($isa_fund,$combined_custody_charge);
	$isa_charges_et   = getPercentageValue($isa_et,$combined_custody_charge);

	$total_charges = $gia_charges_fund+$gia_charges_et+$isa_charges_fund+$isa_charges_et;
	$total_charges = number_format((float)$total_charges, 2, '.', '');

	//echo $total_charges;exit;
	$result = array(	"gia_fund_charge" => $gia_charges_fund,
			"gia_charges_et" => $gia_charges_et,
			"isa_charges_fund" => $isa_charges_fund,
			"isa_charges_et" => $isa_charges_et,
			"total_charges" => $total_charges,
			"total_investment" => $total_investment
		);
	//print '<pre>';print_r($result);
	return $result;
}


/**
 * Start Of  Secound Method Calculation
 * GIA Fund Charges
 *
 */
function getGIAFundChargesFromTier( $fund_amount, $platform ) {
	$total_tier = count($platform['fund']);
	$temp_amount = $fund_amount;
	$index_of_tier = 0;
	$charge = 0.00;
	foreach ($platform['fund'] as $key => $value) {
		if( ($value['bandsfrom'] <= $fund_amount) && ($value['bandsto'] >= $fund_amount) ){
			$charge += getPercentageValue($temp_amount,$value['gia']);
			break;
		}else{
			$index_of_tier++;
			if($index_of_tier == $total_tier ){
				$charge += getPercentageValue($temp_amount,$value['gia']);
				break;
			}
			$charge += getPercentageValue($value['bandsto'],$value['gia']);
			$temp_amount -= $value['bandsto'];
		}
	}

	return  $charge;
}

/**
 * GIA Exchange Trade Charges
 *
 */
function getGIAETChargesFromTier( $et_amount, $platform ) {
	$total_tier = count($platform['et']);
	$temp_amount = $et_amount;
	$index_of_tier = 0;
	$charge = 0.00;
	foreach ($platform['et'] as $key => $value) {
		if( ($value['bandsfrom'] <= $et_amount) && ($value['bandsto'] >= $et_amount) ){
			$charge += getPercentageValue($temp_amount,$value['gia']);
			break;
		}else{
			$index_of_tier++;
			if($index_of_tier == $total_tier ){
				$charge += getPercentageValue($temp_amount,$value['gia']);
				break;
			}
			$charge += getPercentageValue($value['bandsto'],$value['gia']);
			$temp_amount -= $value['bandsto'];
		}
	}
	//echo $charge;exit;
	return  $charge;
}

/**
 * ISA Fund Charges
 *
 */
function getISAFundChargesFromTier( $fund_amount, $platform ) {
	$total_tier = count($platform['fund']);
	$temp_amount = $fund_amount;
	$index_of_tier = 0;
	$charge = 0.00;
	foreach ($platform['fund'] as $key => $value) {
		if( ($value['bandsfrom'] <= $fund_amount) && ($value['bandsto'] >= $fund_amount) ){
			$charge += getPercentageValue($temp_amount,$value['isa']);
			break;
		}else{
			$index_of_tier++;
			if($index_of_tier == $total_tier ){
				$charge += getPercentageValue($temp_amount,$value['isa']);
				break;
			}
			$charge += getPercentageValue($value['bandsto'],$value['isa']);
			$temp_amount -= $value['bandsto'];
		}
	}
	//echo $charge."==";
	return  $charge;
}

/**
 * ISA Exchange Trade Charges
 *
 */
function getISAETChargesFromTier( $et_amount, $platform ) {
	$total_tier = count($platform['et']);
	$temp_amount = $et_amount;
	$index_of_tier = 0;
	$charge = 0.00;
	foreach ($platform['et'] as $key => $value) {
		if( ($value['bandsfrom'] <= $et_amount) && ($value['bandsto'] >= $et_amount) ){
			$charge += getPercentageValue($temp_amount,$value['isa']);
			break;
		}else{
			$index_of_tier++;
			if($index_of_tier == $total_tier ){
				$charge += getPercentageValue($temp_amount,$value['isa']);
				break;
			}
			$charge += getPercentageValue($value['bandsto'],$value['isa']);
			$temp_amount -= $value['bandsto'];
		}
	}
	//echo $charge."==";
	return  $charge;
}

/**
 * caclculate percentage value of amount
 *
 */
function getPercentageValue( $value, $percentage ) {
	$percentage_value = ($percentage / 100) * $value;
	return number_format((float)$percentage_value, 4, '.', '');
	//return $percentage_value;
}

?>