<?php
class WTC_get_saveshipping {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_saveshipping
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
		add_action( 'wtc_get_saveshipping', array( $this, 'get_saveshipping' ) );
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
	
	public function get_saveshipping() {
		
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		$user = $postdata->user_id;
		$shipping_first_name = $postdata->shipping_first_name;
		$shipping_last_name = $postdata->shipping_last_name;
		$shipping_company = $postdata->shipping_company;
		//$shipping_country = $postdata->shipping_country;
		$shipping_country = $postdata->shipping_country_code;
		$shipping_address_1 = $postdata->shipping_address_1;
		$shipping_city = $postdata->shipping_city;
		//$shipping_state = $postdata->shipping_state;
		$shipping_state = $postdata->shipping_state_code;
		$shipping_postcode = $postdata->shipping_postcode;

		//Edit by dev
		$shipping_state_code = $postdata->shipping_state_code;
		$shipping_country_code = $postdata->shipping_country_code;
		//Edit end
		
		$success = 0;
		if($user != ''){
			
			update_user_meta( $user, "shipping_first_name", $shipping_first_name );
			update_user_meta( $user, "shipping_last_name", $shipping_last_name );
			update_user_meta( $user, "shipping_company", $shipping_company );
			update_user_meta( $user, "shipping_country", $shipping_country );
			$shipping_country_code =  WC()->countries->countries[$shipping_country];
			update_user_meta( $user, "shipping_address_1", $shipping_address_1 );
			update_user_meta( $user, "shipping_address_2", $shipping_address_2 );
			update_user_meta( $user, "shipping_city", $shipping_city );
			update_user_meta( $user, "shipping_state", $shipping_state );
			$shipping_state_code =  WC()->countries->states[$shipping_country][$shipping_state];
			update_user_meta( $user, "shipping_postcode", $shipping_postcode );
			//Edit by dev
			update_user_meta( $user, "shipping_state_code", $shipping_state_code );
			update_user_meta( $user, "shipping_country_code", $shipping_country_code );
			//Edit end

			$first_name = get_user_meta($user, 'shipping_first_name', true);
			$last_name = get_user_meta($user, 'shipping_last_name', true);
			$company = get_user_meta($user, 'shipping_company', true);
			$country = get_user_meta($user, 'shipping_country', true);
			$address_1 = get_user_meta($user, 'shipping_address_1', true);
			$address_2 = get_user_meta($user, 'shipping_address_2', true);
			$city = get_user_meta($user, 'shipping_city', true);
			$state = get_user_meta($user, 'shipping_state', true);
			$postcode = get_user_meta($user, 'shipping_postcode', true);
			
			//Edit by dev
			$shipping_state_code = get_user_meta($user, 'shipping_state_code', true);
			$shipping_country_code = get_user_meta($user, 'shipping_country_code', true);
			//Edit end
			
			$shipping_address = array('shipping_first_name' =>$first_name,'shipping_last_name' => $last_name,'shipping_company'=>$company,
			 'shipping_country'=>$shipping_country_code,'shipping_address_1'=>$address_1,'shipping_address_2'=>$address_2,
			 'shipping_city'=>$city,'shipping_state'=>$shipping_state_code,'shipping_postcode'=>$postcode,'shipping_state_code'=>$shipping_state,'shipping_country_code'=>$shipping_country);
			
			
			$result['shipping_address'] = $shipping_address;
			
			$success =1;
			
		}	
		
		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$result));

	}
}
?>
