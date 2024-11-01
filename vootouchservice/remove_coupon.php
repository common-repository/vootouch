<?php
class WTC_get_remove_coupon {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_remove_coupon
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
		add_action( 'wtc_get_remove_coupon', array( $this, 'get_remove_coupon' ) );
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
	
	public function get_remove_coupon() {
		
		//get_remove_coupon
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);
		$code=$postdata->code;
		

		$coupon      = new WC_Coupon( $code );

		if (! $coupon->exists ) {
			$msg = __( 'Sorry there was a problem removing this coupon.', 'woocommerce' );
		}else{
			WC()->cart->remove_coupon( $code );
			$msg =  __( 'Coupon has been removed.', 'woocommerce' );
		}

		header('Content-type: application/json');
		echo json_encode(array('success'=> 1, 'data'=>$code, 'msg'=>$msg));
	}
}
?>
