<?php
class WTC_get_forgetpassword {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_forgetpassword
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
		add_action( 'wtc_get_forgetpassword', array( $this, 'get_forgetpassword' ) );
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
	
public function get_forgetpassword() {
	
	global $wpdb, $wp_hasher;

	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);

	$credentials['user_login']= $postdata->email;

	$errors = new WP_Error();
	$success = 0;
	$result["loginerror"]="";
	if ( empty( $credentials['user_login'] ) ) { 
			$result["loginerror"] = 'Enter a username or email address.';
			$success=0;
		} elseif ( strpos( $credentials['user_login'], '@' ) ) {  
			$user_data = get_user_by( 'email', trim( $credentials['user_login'] ) );

			if ( empty( $user_data ) ){ 
		  $result["loginerror"] = 'There is no user registered with that email address.';
				$success=0;}
		} else {  
			  $login = trim($credentials['user_login']);
			$user_data = get_user_by('login', $login);
			 
	}

	if ( !$user_data ) {
			  $result["loginerror"] = 'Invalid username or email.';
			
	}
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action( 'lostpassword_post', $errors );

	$key = wp_generate_password( 20, false );

	do_action( 'retrieve_password_key', $user_login, $key );

		// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}

	$hashed = $wp_hasher->HashPassword( $key );

	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

	if($result["loginerror"]=="")
	{
	$success=1;
	WC()->mailer(); // load email classes
	do_action( 'woocommerce_reset_password_notification', $user_login, $key );
	}

	if($success == 0){
		
		$result["loginerror"]  = 'Error occured';
		
	}else{
		
		$result["logindata"] = 'successfully user to sending email';
		
	}

	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$result));
	}
}
?>
