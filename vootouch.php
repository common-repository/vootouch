<?php
/*
 * Plugin Name: vootouch
 * Plugin URI:  http://vootouch.com/
 * Description: Woocommerce Mobile Application Plugin. It creat connection  between the Vootouch Mobile Application and WooCommerce website.
 * Author: Lujayninfoways
 * Author URI:  http://www.lujayninfoways.com/ 
 * Version: 2.0
 * License: GPL v3

 * Copyright: (c) 2017-2018, LujaynInfoways (info@lujayninfoways.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   vootouch
 * @author    LujaynInfoways
 * @category  Utility
 * @copyright Copyright (c) 2017-2018, LujaynInfoways.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 
*/ 


if ( ! defined( 'PLUGIN_DIR' ) ) {
	define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WTC_PLUGIN_FILE' ) ) {
	define( 'WTC_PLUGIN_FILE', __FILE__ );
}
 
class VooTouch {


const WEBSERVICE_REWRITE = 'webservice/([a-zA-Z0-9_-]+)$';
	const OPTION_KEY         = 'wtc_options';

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WP_Simple_Web_Service
	 */
	public static function get() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Function that runs on install
	 */
	public static function install() {

		// Clear the permalinks
		flush_rewrite_rules();
		
	}

	/**
	 * Constructor
	 */
	private function __construct() {

		// Load files
		$this->includes();

		// Init
		$this->init();

	}

	/**
	 * Load required files
	 */
	private function includes() {
		
		require_once( PLUGIN_DIR . 'include/include-wtc-rewrite-rules.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/products.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/category.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/subcategory.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/createuser.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/createuser_social.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/hashkey.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/forgetpassword.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/login.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/login_social.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/orders.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/products_detail.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/savebilling.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/saveshipping.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/state.php' );
        require_once( PLUGIN_DIR . 'vootouchservice/change_password.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/country.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/edituser.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/list_review.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/orderdetail.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/remove_coupon.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/save_review.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/setting_option.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/shipping_methods.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/sortcategory.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/applycoupon.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/save_order.php' );
		require_once( PLUGIN_DIR . 'vootouchservice/notification.php' );
		
		
	
		
		if ( is_admin() ) {
			// Backend

			require_once( PLUGIN_DIR . 'include/include-wtc-settings.php' );
			require_once( PLUGIN_DIR . 'include/woocommerce_product_detail.php' );

		}
		else {
			// Frondend

			require_once( PLUGIN_DIR . 'include/include-wtc-catch-request.php' );
			require_once( PLUGIN_DIR . 'include/include-wtc-output.php' );
		}

	}

	/**
	 * Initialize class
	 */
	private function init() {
		
		//Edit by dev
		//register_activation_hook(__FILE__, array(__CLASS__, 'check_plugin_activated'));
		//add_action('admin_init', array(__CLASS__, 'check_plugin_activated'));
		//Edit end
		
		// Setup Rewrite Rules
		WTC_Rewrite_Rules::get();

		// Default webservice
		WTC_get_products::get();
		WTC_get_category::get();
		WTC_get_subcategory::get();
		WTC_get_createuser::get();
		WTC_get_createuser_social::get();
		wtc_get_hashkey::get();
		WTC_get_forgetpassword::get();
		WTC_get_login::get();
		WTC_get_login_social::get();
		WTC_get_orders::get();
		WTC_get_products_detail::get();
		WTC_get_savebilling::get();
		WTC_get_saveshipping::get();
		WTC_get_state::get();
		WTC_get_country::get();
		WTC_get_change_password::get();
		WTC_get_edituser::get();
		WTC_get_list_review::get();
		WTC_get_orderdetail::get();
		WTC_get_remove_coupon::get();
		WTC_get_save_review::get();
		WTC_get_setting_option::get();
		WTC_get_shipping_methods::get();
		WTC_get_sortcategory::get();
		WTC_get_save_order::get();
		WTC_get_applycoupon::get();
		WTC_get_notification::get();
		
		
		if ( is_admin() ) {
			// Backend

			// Setup settings
			WTC_Settings::get();
			WTC_get_woocommerce_product_detail::get();
	
		}
		else {
			// Frondend

			// Catch request
			WTC_Catch_Request::get();
		}

	}
	
	/**
	 * The correct way to throw an error in a webservice
	 *
	 * @param $error_string
	 */
	public function throw_error( $error_string ) {
		wp_die( '<b>Webservice error:</b> ' . $error_string );
		
	}

	/**
	 * Function to get the plugin options
	 *
	 * @return array
	 */
	public function get_options() {
		return get_option( self::OPTION_KEY, array() );
	}

	/**
	 * Function to save the plugin options
	 *
	 * @param $options
	 */
	public function save_options( $options ) {
		update_option( self::OPTION_KEY, $options );
	}
	
	public static function check_plugin_activated() {
				
    $plugin = is_plugin_active("woocommerce/woocommerce.php");
    
    if (!$plugin) {
      deactivate_plugins(plugin_basename(__FILE__));
      add_action('admin_notices', array(__CLASS__, 'disabled_notice'));
      if (isset($_GET['activate']))
        unset($_GET['activate']);
    }
    
  }

	public static function disabled_notice() {
    global $current_screen;
    if ($current_screen->parent_base == 'plugins'):
      ?>
      <div class="error" style="padding: 8px 8px;">
        <strong>
          <?= __('WOO MALL requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> activated in order to work. Please install and activate <a href="' . admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce') . '" target="_blank">WooCommerce</a> first.') ?>
        </strong>
      </div>
      <?php
    endif;
  }
    
}
  /**
	 *
	 * @Create vootouch setting options
	 */
	
 
  	function WTC_create_db() {
			
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'wtc_settings';
		$screen_url =  plugin_dir_url(WTC_PLUGIN_FILE ) . '/images/logo.png';
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL,
			image_url varchar(255) NOT NULL,
			back_color tinytext NOT NULL,
			font_color tinytext NOT NULL,
			th_back_color tinytext NOT NULL,
			icon_color tinytext NOT NULL,
			page_shape_color tinytext NOT NULL,
			name_title_color tinytext NOT NULL,
			splash_screen_color tinytext NOT NULL,
			terms longtext NOT NULL,
			contact longtext NOT NULL,
            paypal_id varchar(255) NOT NULL,
			page_view int(11) NOT NULL DEFAULT '0',
			product_view int(11) NOT NULL DEFAULT '0',
			category_view int(11) NOT NULL DEFAULT '0',			
			UNIQUE KEY id (id)
			)";

		
		dbDelta( $sql );
	
		$table_name1 = $wpdb->prefix . 'wtc_notification';
	
		$sql = "CREATE TABLE $table_name1 (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			token varchar(255) NOT NULL,
                        type varchar(255) NOT NULL,
			 PRIMARY KEY (id),
			 UNIQUE KEY token (token)
			)";
		
		dbDelta( $sql );
		
		$table_name2 = $wpdb->prefix . 'wtc_social';
	
		$sql = "CREATE TABLE $table_name2 (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			fb_user_id varchar(255) NOT NULL,
			user_name varchar(255) NOT NULL,
			user_lname varchar(255) NOT NULL,
			user_email varchar(255) NOT NULL,			
			 PRIMARY KEY (id),
			 UNIQUE KEY fb_user_id (fb_user_id)
			)";
		
		dbDelta( $sql );
		
	$wpdb->insert( 
		$table_name, 
		array( 
			'id' => 1, 
			'image_url'  => $screen_url, 
			'back_color' => '#96588a',
			'font_color' => '#ffffff',
			'th_back_color' => '#96588a',
			'icon_color' => '#ffffff',
			'page_shape_color' => '#96588a',
			'name_title_color' => '#ffffff',
			'splash_screen_color' => '#96588a',
			'terms'		 => 'Terms And Condition',
			'contact'	 => '<h3>Address</h3>
			A-606, Fairdeal House, Near Swastik<br />
			Society Cross Road, Navrangpura,<br />
			Ahmedabad, Gujarat 380009.<br />
			<h3>Email</h3><br />
			<a href="mailto:info@vootouch.com">info@vootouch.com</a>
			<br /><br />
			<a href="mailto:sales@vootouch.com">sales@vootouch.com</a>
			<h3>phone</h3><br />
			+91 9974845340<br />
			<br /><br />
			+91 9898837321'
		) 
	);
	
	}
	
  /***********/
	register_activation_hook( __FILE__, 'WTC_create_db' ); 
	/****Delete Plugin Remove Tables *****/
 
	function WTC_deactivation()
	{
		
			global $wpdb;
			$table_name = $wpdb->prefix . "wtc_settings";
			$sql = "DROP TABLE IF EXISTS $table_name";
			$wpdb->query($sql);
			
			$table_name1 = $wpdb->prefix . "wtc_notification";
			$sql = "DROP TABLE IF EXISTS $table_name1";
			$wpdb->query($sql);
			
			$table_name2 = $wpdb->prefix . "wtc_social";
			$sql = "DROP TABLE IF EXISTS $table_name2";
			$wpdb->query($sql);
			//die($sql);
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			delete_option("WTC_db_version");
			
	}
	
	//register_uninstall_hook( __FILE__, 'WTC_deactivation' );
	register_deactivation_hook( __FILE__, 'WTC_deactivation' );

	function VooTouch() {
		return VooTouch::get();
	}

// Load plugin
add_action( 'plugins_loaded', create_function( '', 'VooTouch::get();' ) );

// Install hook
register_activation_hook( PLUGIN_DIR, array( 'VooTouch', 'install' ) );
//register_deactivation_hook( PLUGIN_DIR, array( 'VooTouch', 'uninstall' ) );
?>
