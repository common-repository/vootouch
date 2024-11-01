<?php
class WTC_get_createuser{
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_createuser
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
		add_action( 'wtc_get_createuser', array( $this, 'get_createuser' ) );
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
	
	public function get_createuser() {
	
	global $wpdb ,$woocommerce;

	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);

	$showName=$postdata->billing_first_name;
	$user_name = $postdata->user_name;
	$random_password = $postdata->password;
        $google = $postdata->google;
	$user_email = $postdata->billing_email;
	$lname = $postdata->billing_last_name;

	$billing_first_name = $postdata->billing_first_name;
	$billing_last_name = $postdata->billing_last_name;
	$billing_address_1 = $postdata->billing_address_1;
	$billing_company = $postdata->billing_company;
	$billing_city = $postdata->billing_city;
	$billing_state = $postdata->billing_state;
	$billing_country = $postdata->billing_country;
	$billing_email = $postdata->billing_email;
	$billing_postcode = $postdata->billing_postcode;
	$billing_phone = $postdata->billing_phone;

	$shipping_first_name = $postdata->shipping_first_name;
	$shipping_last_name = $postdata->shipping_last_name;
	$shipping_country = $postdata->shipping_country;
	$shipping_company = $postdata->shipping_company;
	$shipping_address_1 = $postdata->shipping_address_1;
	$shipping_city = $postdata->shipping_city;
	$shipping_state = $postdata->shipping_state;
	$shipping_postcode = $postdata->shipping_postcode;


        $user_id = wc_create_new_customer(
        $user_email, $user_name, $random_password, $lname,
        $billing_first_name,$billing_last_name,$billing_company,$billing_email,$billing_phone,
        $billing_country,$billing_address_1,$billing_city,$billing_state,$billing_postcode,
        $shipping_first_name,$shipping_last_name,$shipping_company,$shipping_country,$shipping_address_1,
        $shipping_city,$shipping_state,$shipping_postcode);
        
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $showName, 'first_name'=>$billing_first_name,'last_name' =>$lname) );

		$success="";
	if( is_wp_error( $user_id ) ) {

	    $err_code=array("registererror" => $user_id->get_error_code());
            if($err_code="registration-error-email-exists") {
                  $success=3;
                  $result=array("registererror" => $user_id->get_error_message());
            }else{
		$success=0;
		$result=array("registererror" => $user_id->get_error_message());
            }    
			 
	} else {
		$success=1;
		$credentials['user_login']=$user_name;
		$credentials['user_password']=$random_password;
		$loginresult=wp_signon($credentials);

		if( is_wp_error( $loginresult ) ) {
		$result["loginerror"]=  $loginresult->get_error_message();
		}else{
		update_user_meta( $user_id, "billing_first_name", $billing_first_name );
		update_user_meta( $user_id, "billing_last_name", $billing_last_name );
		update_user_meta( $user_id, "billing_company", $billing_company );
		update_user_meta( $user_id, "billing_email", $billing_email );
		update_user_meta( $user_id, "billing_phone", $billing_phone );
		update_user_meta( $user_id, "billing_country", $billing_country );
		update_user_meta( $user_id, "billing_address_1", $billing_address_1 );
		update_user_meta( $user_id, "billing_city", $billing_city );
		update_user_meta( $user_id, "billing_state", $billing_state );
		update_user_meta( $user_id, "billing_postcode", $billing_postcode );
		
		if(!empty($shipping_first_name)){
		update_user_meta( $user_id, "shipping_first_name", $shipping_first_name );
		}
		if(!empty($shipping_last_name)){
		update_user_meta( $user_id, "shipping_last_name", $shipping_last_name );
		}
		if(!empty($shipping_company)){
		update_user_meta( $user_id, "shipping_company", $shipping_company );
		}
		if(!empty($shipping_country)){
		update_user_meta( $user_id, "shipping_country", $shipping_country );
		}
		if(!empty($shipping_address_1)){
		update_user_meta( $user_id, "shipping_address_1", $shipping_address_1 );
		}
		//update_user_meta( $user_id, "shipping_address_2", $shipping_address_2 );
		if(!empty($shipping_city)){
		update_user_meta( $user_id, "shipping_city", $shipping_city );
		}
		if(!empty($shipping_state)){
		update_user_meta( $user_id, "shipping_state", $shipping_state );
		}
		if(!empty($shipping_postcode)){
		update_user_meta( $user_id, "shipping_postcode", $shipping_postcode );
		}
		update_user_meta( $user_id, "google", $google );
		//$user_id = 43;
		$first_name = get_user_meta($user_id, 'billing_first_name', true);
		$last_name = get_user_meta($user_id, 'billing_last_name', true);
		$company = get_user_meta($user_id, 'billing_company', true);
		$email = get_user_meta($user_id, 'billing_email', true);
		$phone = get_user_meta($user_id, 'billing_phone', true);
		$country_code = get_user_meta($user_id, 'billing_country', true);
		if(!empty($country_code)){
			$bill_country =  WC()->countries->countries[$country_code];
		}else{
			$bill_country = $country_code;
		}
		$address_1 = get_user_meta($user_id, 'billing_address_1', true);
		$city = get_user_meta($user_id, 'billing_city', true);
		$state_code = get_user_meta($user_id, 'billing_state', true);
		if(!empty($state_code)){
			$state =  WC()->countries->states[$country_code][$state_code];
		}else{
			$state = $state_code;
		}
		$postcode = get_user_meta($user_id, 'billing_postcode', true);
		
		$ship_first_name = get_user_meta($user_id, 'shipping_first_name', true);
		$ship_last_name = get_user_meta($user_id, 'shipping_last_name', true);
		$ship_company = get_user_meta($user_id, 'shipping_company', true);
		$scountry_code = get_user_meta($user_id, 'shipping_country', true);
		if(!empty($scountry_code)){
			$ship_country =  WC()->countries->countries[$scountry_code];
		}else{
			$ship_country = $scountry_code;
		}
		$ship_address_1 = get_user_meta($user_id, 'shipping_address_1', true);
		$ship_city = get_user_meta($user_id, 'shipping_city', true);
		$ship_state_code = get_user_meta($user_id, 'shipping_state', true);
		if(!empty($ship_state_code)){
			$ship_state =  WC()->countries->states[$scountry_code][$ship_state_code];
		}else{
			$ship_state = $ship_state_code;
		}
		$ship_postcode = get_user_meta($user_id, 'shipping_postcode', true);
		
		$billing_address = array('billing_first_name' =>$first_name,'billing_last_name' => $last_name,'billing_company'=>$company,
		 'billing_email'=>$email,'billing_phone'=>$phone,'billing_country'=>$bill_country,'billing_address_1'=>$address_1,
		 'billing_city'=>$city,'billing_state'=>$state,'billing_postcode'=>$postcode,'billing_country_code'=>$country_code,'billing_state_code'=>$state_code);
		
		$shipping_address = array('shipping_first_name' =>$ship_first_name,'shipping_last_name' => $ship_last_name,'shipping_company'=>$ship_company,
			'shipping_country'=>$ship_country,'shipping_address_1'=>$ship_address_1,
			'shipping_city'=>$ship_city,'shipping_state'=>$ship_state,'shipping_postcode'=>$ship_postcode,'shipping_country_code'=>$scountry_code,'shipping_state_code'=>$ship_state_code);
		
		$result['billing_address'] = $billing_address;
		$result['shipping_address'] = $shipping_address;
		//user info
		$userinfo=get_userdata($user_id);
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
		}
	}
	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$result));
	}
}
?>
