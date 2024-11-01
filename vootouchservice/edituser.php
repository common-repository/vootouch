<?php
class WTC_get_edituser {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_edituser
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
		add_action( 'wtc_get_edituser', array( $this, 'get_edituser' ) );
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
	
	public function get_edituser() {
		
		global $wpdb ,$woocommerce;

		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);
		
		$user = $postdata->user_id;

		$fname = $postdata->first_name;
		$lname = $postdata->last_name;
		$email = $postdata->email;
		$dp_name = $postdata->display_name;
		$user_first_name = $postdata->first_name;
		$user_last_name = $postdata->last_name;
			
		$billing_first_name = $postdata->billing_first_name;
		$billing_last_name = $postdata->billing_last_name;
		$billing_company = $postdata->billing_company;
		$billing_email = $postdata->billing_email;
		$billing_phone = $postdata->billing_phone;
		$billing_country = $postdata->billing_country_code;
		$billing_address_1 = $postdata->billing_address_1;
		$billing_address_2 = $postdata->billing_address_2;
		$billing_city = $postdata->billing_city;
		$billing_state = $postdata->billing_state_code;
		$billing_postcode = $postdata->billing_postcode;

		$shipping_first_name = $postdata->shipping_first_name;
		$shipping_last_name = $postdata->shipping_last_name;
		$shipping_company = $postdata->shipping_company;
		$shipping_country = $postdata->shipping_country_code;
		$shipping_address_1 = $postdata->shipping_address_1;
		$shipping_address_2 = $postdata->shipping_address_2;
		$shipping_city = $postdata->shipping_city;
		$shipping_state = $postdata->shipping_state_code;
		$shipping_postcode = $postdata->shipping_postcode;
		
		if($user != ''){
			
			/*Billing Data*/
			
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
			$billing_state_code =  WC()->countries->states[$billing_country][$billing_state];
			update_user_meta( $user, "billing_postcode", $billing_postcode );
			/*End */
			
			/*shipping Data*/
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
		 
			$billing_address = array('billing_first_name' =>$billing_first_name,'billing_last_name' => $billing_last_name,'billing_company'=>$billing_company,
			 'billing_email'=>$billing_email,'billing_phone'=>$billing_phone,'billing_country'=>$billing_country_code,'billing_address_1'=>$billing_address_1,'billing_address_2'=>$billing_address_2,
			 'billing_city'=>$billing_city,'billing_state'=>$billing_state_code,'billing_postcode'=>$billing_postcode,'billing_country_code'=>$billing_country,'billing_state_code'=>$billing_state);
			
			$shipping_address = array('shipping_first_name' =>$shipping_first_name,'shipping_last_name' => $shipping_last_name,'shipping_company'=>$shipping_company,
			 'shipping_country'=>$shipping_country_code,'shipping_address_1'=>$shipping_address_1,'shipping_address_2'=>$shipping_address_2,
			 'shipping_city'=>$shipping_city,'shipping_state'=>$shipping_state_code,'shipping_postcode'=>$shipping_postcode,'shipping_country_code'=>$shipping_country,'shipping_state_code'=>$shipping_state);
				
			 $uinfo = get_userdata( $user );
		
			$args = array(
			'ID' => $user,
			'user_nicename' => $fname,
			'last_name' => $lname,
			'user_email' => $email,
			'display_name' => $dp_name,
			'user_first_name' => $user_first_name,
			'user_last_name' => $user_last_name,
			);
			wp_update_user( $args );

			update_user_meta($user, 'first_name', $fname);
			update_user_meta($user, 'last_name', $lname);
			update_user_meta($user, 'email', $email);
			update_user_meta($user, 'display_name', $dp_name);
			 
			$result['user_data'] = $args;
			$result['billing_address'] = $billing_address;
			$result['shipping_address'] = $shipping_address;
			$success =1;
			
		}else{
			$success =0;
		}

		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$result));
	}
}
?>
