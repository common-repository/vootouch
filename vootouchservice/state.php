<?php
class WTC_get_state {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_state
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
		add_action( 'wtc_get_state', array( $this, 'get_state' ) );
		add_action( 'wtc_general_settings', array( $this, 'settings' ), 1 );
	}

	/**
	 * get_posts settings screen
	 */
	 public function settings() {
		
	}

	/**
	 * Function to get the default settings
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array( 'enabled' => 'false', 'fields' => array(), 'custom' => array() );
	}
	
	public function get_state() {
		
		$postdata = file_get_contents("php://input");

		$postdata = json_decode($postdata);
		$country_code = $postdata->country_code;

		global $woocommerce;

		$countries_obj = new WC_Countries();
		$countries   = $countries_obj->__get('countries');
		
		$state = $countries_obj->get_states($country_code);

		$success = 0;


		$con = array();
		$mycon = array();
		foreach ($state as $key => $val) {

			 $mycon[] = array( 'state_code' => $key, 'state_name' => html_entity_decode($val));
			 
		}

		$con['state_list'] = $mycon;

		if($con) {
		 
		 $success =1;	

		}

		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$con));
	}
}
?>
