<?php
class WTC_get_login {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_login
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
		add_action( 'wtc_get_login', array( $this, 'get_login' ) );
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
	
	public function get_login() {
	
	global $wpdb ,$woocommerce;
	$postdata = file_get_contents("php://input");
 
	$postdata = json_decode($postdata);;
	$user_name=$postdata->username;
	$random_password=$postdata->password;
	 
	 
	$credentials['user_login']=$user_name;
	$credentials['user_password']=$random_password;
	
	$loginresult=wp_signon($credentials);
	$success="";

		if( is_wp_error( $loginresult ) ) {
			$success=0;
			$result["loginerror"]=  strip_tags($loginresult->get_error_message());
		 
		} 
		else
		{	 
			$userinfo=get_userdata($loginresult->ID);
			$success=1;
			$user = $loginresult->ID;
			$user_first_name = $userinfo->first_name;
			$user_last_name = $userinfo->last_name;
			$ulogin = $userinfo->data->user_login;
			$upass = $userinfo->data->user_pass;
			$user_nicename = $userinfo->data->user_nicename;
			$user_email = $userinfo->data->user_email;
			$display_name = $userinfo->data->display_name;
			$args = array(
			'ID' => $user,
			'user_login' => $ulogin,
			'user_pass' => $upass,
			'user_nicename' => $user_nicename,
			'user_email' => $user_email,
			'display_name' => $display_name,
			'user_first_name' => $user_first_name,
			'user_last_name' => $user_last_name,
			);
			$result["userinfo"]= $args;
			
			//billing address
			$user = $loginresult->ID;
			$first_name = get_user_meta($user, 'billing_first_name', true);
			$last_name = get_user_meta($user, 'billing_last_name', true);
			$company = get_user_meta($user, 'billing_company', true);
			$email = get_user_meta($user, 'billing_email', true);
			$phone = get_user_meta($user, 'billing_phone', true);
			$country_code = get_user_meta($user, 'billing_country', true); 
					
			if(empty($country_code)){
								
				$country = '';
			
			}else{
			
				 $country =  WC()->countries->countries[$country_code]; 
			
			}
			
			$address_1 = get_user_meta($user, 'billing_address_1', true);
			$address_2 = get_user_meta($user, 'billing_address_2', true);
			$city = get_user_meta($user, 'billing_city', true);
			$state_code = get_user_meta($user, 'billing_state', true);
			
			if(empty($state_code)){
				$state = '';
			}else{
				$state =  WC()->countries->states[$country_code][$state_code];
			}
			
			$postcode = get_user_meta($user, 'billing_postcode', true);
			
			//shipping address
			$shipping_first_name = get_user_meta($user, 'shipping_first_name', true);
			$shipping_last_name = get_user_meta($user, 'shipping_last_name', true);
			$shipping_company = get_user_meta($user, 'shipping_company', true);
			$shipping_country_code = get_user_meta($user, 'shipping_country', true);
			
			if(empty($shipping_country_code)){
				
				
				$shipping_country = '';
			
			}else{
			
				$shipping_country =  WC()->countries->countries[$shipping_country_code];
			
			}
			
			$shipping_address_1 = get_user_meta($user, 'shipping_address_1', true);
			$shipping_address_2 = get_user_meta($user, 'shipping_address_2', true);
			$shipping_city = get_user_meta($user, 'shipping_city', true);
			$shipping_state_code = get_user_meta($user, 'shipping_state', true);
			
			if(empty($shipping_state_code)){
				
				$shipping_state = '';
				
			}else{
				
				$shipping_state =  WC()->countries->states[$shipping_country_code][$shipping_state_code];
			
			}
			
			$shipping_postcode = get_user_meta($user, 'shipping_postcode', true);
			
			
			$billing_address = array('billing_first_name' =>$first_name,'billing_last_name' => $last_name,'billing_company'=>$company,
			'billing_email'=>$email,'billing_phone'=>$phone,'billing_country'=>$country,'billing_address_1'=>$address_1,'billing_address_2'=>$address_2,
			'billing_city'=>$city,'billing_state'=>$state,'billing_postcode'=>$postcode,'billing_country_code'=>$country_code,'billing_state_code'=>$state_code);
			
			$shipping_address = array('shipping_first_name' =>$shipping_first_name,'shipping_last_name' => $shipping_last_name,'shipping_company'=>$shipping_company,
			'shipping_country'=>$shipping_country,'shipping_address_1'=>$shipping_address_1,'shipping_address_2'=>$shipping_address_2,
			'shipping_city'=>$shipping_city,'shipping_state'=>$shipping_state,'shipping_postcode'=>$shipping_postcode,'shipping_country_code'=>$shipping_country_code,'shipping_state_code'=>$shipping_state_code);
			
			$result['billing_address'] = $billing_address;
			$result['shipping_address'] = $shipping_address;
			
		}

	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$result));
	}
}	
?>
