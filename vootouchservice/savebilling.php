<?php
class WTC_get_savebilling {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_savebilling
	 */
	public static function get() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_action( 'wtc_get_savebilling', array( $this, 'get_savebilling' ) );
		add_action( 'wtc_general_settings', array( $this, 'settings' ), 1 );
	}

	/**
	 * get_posts settings screen
	 */
	public function settings() {
		/*********/
		// Do Your Settings Stuff Here
		/**************/
	}

	/**
	 * Function to get the default settings
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array( 'enabled' => 'false', 'fields' => array(), 'custom' => array() );
	}
	
	public function get_savebilling() {
	
	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);

	$user = $postdata->user_id;
	$billing_first_name = $postdata->billing_first_name;
	$billing_last_name = $postdata->billing_last_name;
	$billing_company = $postdata->billing_company;
	$billing_email = $postdata->billing_email;
	$billing_phone = $postdata->billing_phone;
	//$billing_country = $postdata->billing_country;
	$billing_country = $postdata->billing_country_code;
	$billing_address_1 = $postdata->billing_address_1;
	$billing_city = $postdata->billing_city;
	//$billing_state = $postdata->billing_state;
	$billing_state = $postdata->billing_state_code;
	$billing_postcode = $postdata->billing_postcode;
	//Edit by dev
	$billing_state_code = $postdata->billing_state_code;
	$billing_country_code = $postdata->billing_country_code;
	//Edit end

	$success = 0;

	if($user != ''){
		
		update_user_meta( $user, "billing_first_name", $billing_first_name );
		update_user_meta( $user, "billing_last_name", $billing_last_name );
		update_user_meta( $user, "billing_company", $billing_company );
		update_user_meta( $user, "billing_email", $billing_email );
		update_user_meta( $user, "billing_phone", $billing_phone );
		update_user_meta( $user, "billing_country", $billing_country );
		$billing_country_code =  WC()->countries->countries[$billing_country];
		update_user_meta( $user, "billing_address_1", $billing_address_1 );
		update_user_meta( $user, "billing_address_2", $billing_address_2 );
		update_user_meta( $user, "billing_city", $billing_city );
		update_user_meta( $user, "billing_state", $billing_state );
		update_user_meta( $user, "billing_postcode", $billing_postcode );
		//Edit by dev
		update_user_meta( $user, "billing_state_code", $billing_state_code );
		$billing_state =  WC()->countries->states[$billing_country][$billing_state];
		update_user_meta( $user, "billing_country_code", $billing_country_code );
		//Edit end

		$first_name = get_user_meta($user, 'billing_first_name', true);
		$last_name = get_user_meta($user, 'billing_last_name', true);
		$company = get_user_meta($user, 'billing_company', true);
		$email = get_user_meta($user, 'billing_email', true);
		$phone = get_user_meta($user, 'billing_phone', true);
		$country = get_user_meta($user, 'billing_country', true);
		$address_1 = get_user_meta($user, 'billing_address_1', true);
		$address_2 = get_user_meta($user, 'billing_address_2', true);
		$city = get_user_meta($user, 'billing_city', true);
		$state = get_user_meta($user, 'billing_state', true);
		$postcode = get_user_meta($user, 'billing_postcode', true);
		
		//Edit by dev
		$billing_state_code = get_user_meta($user, 'billing_state_code', true);
		$billing_country_code = get_user_meta($user, 'billing_country_code', true);		
		//Edit end
		
		
		
		$billing_address = array('billing_first_name' =>$first_name,'billing_last_name' => $last_name,'billing_company'=>$company,
		 'billing_email'=>$email,'billing_phone'=>$phone,'billing_country'=>$billing_country_code,'billing_address_1'=>$address_1,'billing_address_2'=>$address_2,
		 'billing_city'=>$city,'billing_state'=>$billing_state,'billing_postcode'=>$postcode,'billing_state_code'=>$billing_state_code,'billing_country_code'=>$country);
		
		$result['billing_address'] = $billing_address;
				
		$success = 1;
		
	}	
	
	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$result));
	
	}
}
?>
