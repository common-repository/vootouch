<?php
class WTC_get_applycoupon {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_applycoupon
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
		add_action( 'wtc_get_applycoupon', array( $this, 'get_applycoupon' ) );
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
	
	public function get_applycoupon() {
		
		global $wpdb, $woocommerce;

		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		$code = $postdata->code;
		
		$success = 0;
		$coupon      = new WC_Coupon( $code );
		if(!empty($code)){
		$expiry_date = $coupon->expiry_date ? wc_rest_prepare_date_response( date( 'Y-m-d', $coupon->expiry_date ) ) : null;
		$coupon_data = array(
			'id'                           => $coupon->id,
			'code'                         => $coupon->code,
			'type'                         => $coupon->type,
			'amount'                       => wc_format_decimal( $coupon->coupon_amount, 2 ),
			'individual_use'               => ( 'yes' === $coupon->individual_use ),
			'product_ids'                  => array_map( 'absint', (array) $coupon->product_ids ),
			'exclude_product_ids'          => array_map( 'absint', (array) $coupon->exclude_product_ids ),
			'usage_limit'                  => ( ! empty( $coupon->usage_limit ) ) ? $coupon->usage_limit : null,
			'usage_limit_per_user'         => ( ! empty( $coupon->usage_limit_per_user ) ) ? $coupon->usage_limit_per_user : null,
			'limit_usage_to_x_items'       => (int) $coupon->limit_usage_to_x_items,
			'usage_count'                  => (int) $coupon->usage_count,
			'expiry_date'                  => date( 'Y-m-d', strtotime( $expiry_date ) ),
			'enable_free_shipping'         => $coupon->enable_free_shipping(),
			'product_category_ids'         => array_map( 'absint', (array) $coupon->product_categories ),
			'exclude_product_category_ids' => array_map( 'absint', (array) $coupon->exclude_product_categories ),
			'exclude_sale_items'           => $coupon->exclude_sale_items(),
			'minimum_amount'               => wc_format_decimal( $coupon->minimum_amount, 2 ),
			'maximum_amount'               => wc_format_decimal( $coupon->maximum_amount, 2 ),
			'customer_emails'              => $coupon->customer_email,
			'description'                  => $coupon->post_excerpt,
		);
		
	   }
		
		if ($coupon->is_valid() ) {
			$success = 1;
			$err = "Coupon code applied successfully.";
			$result = array( 'coupons' => $coupon_data , 'err_msg' =>$err);
			
		}else{
			$success = 0;
			$result['err_msg'] = "Coupon code is not valid.";
		}
		
		header('Content-type: application/json');
		echo json_encode(array('success'=> $success,'data'=>$result));
	}
}
?>
