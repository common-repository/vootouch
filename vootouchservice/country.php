<?php
class WTC_get_country {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_country
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
		add_action( 'wtc_get_country', array( $this, 'get_country' ) );
		add_action( 'wtc_general_settings', array( $this, 'settings' ), 1 );
	}

	/**
	 * get_posts settings screen
	 */
	 public function settings() {
		/* Setting Stuff */
	}

	/**
	 * Function to get the default settings
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array( 'enabled' => 'false', 'fields' => array(), 'custom' => array() );
	}
	
	public function get_country() {
		
		global $woocommerce;

		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		$countries_obj   = new WC_Countries();
	
		$countries = $countries_obj->get_allowed_countries();
		
		/*	
		$countries   = $countries_obj->__get('countries'); 	
		$default_country = $countries_obj->get_base_country();
		$default_county_states = $countries_obj->get_states( $array );
		*/		
				
		$success = 0;

		$con = array();
		$mycon = array();
		foreach ($countries as $key => $val) {

			 $mycon[] = array( 'country_code' => $key, 'country_name' => html_entity_decode($val));
			 
		}

		$con['country_list'] = $mycon;
				
		if($con) {
		 
		 $success =1;	

		}
		
		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$con));
	}
}
?>
