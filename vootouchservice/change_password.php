<?php
class WTC_get_change_password {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_change_password
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
		add_action( 'wtc_get_change_password', array( $this, 'get_change_password' ) );
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
	
	public function get_change_password() {
		
		global $wpdb ,$woocommerce;
		 
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		$user_id = $postdata->user_id;
		
		$new_password = $postdata->new_password;
		$pass = $postdata->password;

		$user = get_user_by( 'id', $user_id );

		if ( $user && wp_check_password( $pass, $user->data->user_pass, $user_id) ){
			//   echo "That's it"; exit;
			wp_set_password( $new_password, $user_id );
			$success = 1;
			$result['msg'] = 'Password changed successfully.';
		}else{
			$success = 0;
			$result['msg'] = 'Password Does not Match.';
		}
		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$result));
	}
}
?>
