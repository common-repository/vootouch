<?php

class WTC_get_setting_option {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_setting_option
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
		add_action( 'wtc_get_setting_option', array( $this, 'get_setting_option' ) );
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
	
	public function get_setting_option() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'wtc_settings';
		$sql_all = "SELECT * FROM $table_name";
		$results = $wpdb->get_results($sql_all);
		foreach( $results as $result ) {
			$bk_color =  $result->back_color;
			$screen_url =  $result->image_url;
			$fnt_color =  $result->font_color;
                        $th_back_color =  $result->th_back_color;
			$icon_color =  $result->icon_color;
			$page_shape_color =  $result->page_shape_color;
			$name_title_color =  $result->name_title_color;
			$splash_screen_color = $result->splash_screen_color;
			$terms =  $result->terms;
			$contact =  $result->contact;
			$page_view = $result->page_view;
			$product_view = $result->product_view;		
			$category_view = $result->category_view;	
		}
	
		$demo_mode = 'yes';
	
		if($page_view == 0){
			$page_view = 'category';
		}else{
			$page_view = 'shop';	
		}
	
	$location['base_location']  = array(
	'woocommerce_default_country'							=> get_option('woocommerce_default_country'),
	'woocommerce_allowed_countries'							=> get_option('woocommerce_allowed_countries'),
	'woocommerce_ship_to_countries'							=> get_option('woocommerce_ship_to_countries'),
	'woocommerce_default_customer_address'					        => get_option('woocommerce_default_customer_address'),
	);
	
				
	$option = array(
		'enable_ratings_on_reviews'				=> get_option( 'woocommerce_enable_review_rating' ),
		'ratings_are_required_to_leave_a_review'		=> get_option( 'woocommerce_review_rating_required' ),
		'manage_stock'						=> get_option( 'woocommerce_manage_stock' ),
		'weight_unit'                           		=> get_option( 'woocommerce_weight_unit' ),
		'dimension_unit'                       			=> get_option( 'woocommerce_dimension_unit' ),
		'download_method'                    			=> get_option( 'woocommerce_file_download_method' ),
		'download_require_login'               			=> get_option( 'woocommerce_downloads_require_login' ),
		'calc_taxes'                            		=> get_option( 'woocommerce_calc_taxes' ),
		'coupons_enabled'                     			=> get_option( 'woocommerce_enable_coupons' ),
		'guest_checkout'                        		=> get_option( 'woocommerce_enable_guest_checkout'),
		'secure_checkout'                       		=> get_option( 'woocommerce_force_ssl_checkout' ),
		'enable_signup_and_login_from_checkout' 		=> get_option( 'woocommerce_enable_signup_and_login_from_checkout' ),
		'enable_myaccount_registration'         		=> get_option( 'woocommerce_enable_myaccount_registration' ),
		'registration_generate_username'        		=> get_option( 'woocommerce_registration_generate_username' ),
		'registration_generate_password'        		=> get_option( 'woocommerce_registration_generate_password' ),
		'currency_symbol'					=> html_entity_decode(get_woocommerce_currency_symbol()),
                'currency_code'                  			=> get_woocommerce_currency(),
		'decimal_separator'					=> wc_get_price_decimal_separator(),
		'thousand_separator'				        => wc_get_price_thousand_separator(),
		'decimals_point'					=> wc_get_price_decimals(),
		'price_format'						=> get_woocommerce_price_format(),
		'splash_screen'						=> $screen_url,
		'toolbar_title_color'					=> $fnt_color,
		'status_bar_color'					=> $bk_color,
		'toolbar_back_color'					=> $th_back_color,
		'toolbar_icon_color'					=> $icon_color,
		'cate_shape_color'					=> $page_shape_color,
		'cate_name_color'					=> $name_title_color,
		'splash_screen_color'                                   => $splash_screen_color,
		'terms_and_condition'					=> $terms,
		'contact_info'						=> $contact,
		'page_view' 						=> $page_view,			
		'prices_include_tax'				        => get_option( 'woocommerce_prices_include_tax' ),
		'shipping_tax_class'					=> get_option( 'woocommerce_shipping_tax_class' ),		
		'tax_display_shop'					=> get_option( 'woocommerce_tax_display_shop' ),
		'tax_display_cart'					=> get_option('woocommerce_tax_display_cart'),
		'enable_taxes'						=> get_option('woocommerce_calc_taxes'),
		'tax_based_on'						=> get_option('woocommerce_tax_based_on'),
		'base_location'						=> $location['base_location'] ,
		'demo_mode'                                             => $demo_mode,
		'product_view'                                          => $product_view,
		'category_view'			                        => $category_view,
                'version'                                               => '1.10.26',
	);
			
		$option=array("options" => $option);
				
		header('Content-type: application/json');
		echo json_encode(array('success'=> 1, 'data'=>$option));
	}
}
?>
