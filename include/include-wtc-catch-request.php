<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WTC_Catch_Request {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_Catch_Request
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
		add_action( 'template_redirect', array( $this, 'handle_request' ) );
	}

	/**
	 * Handle webservice request
	 */
	public function handle_request() {

		global $wp_query;


		if ( $wp_query->get( 'webservice' ) ) {

			if ( $wp_query->get( 'vootouchservice' ) != '' ) {

				// Check if the action exists
				if ( has_action( 'wtc_' . $wp_query->get( 'vootouchservice' ) ) ) {

					// Do action
					do_action( 'wtc_' . $wp_query->get( 'vootouchservice' ) );

					// Bye
					exit;
				}

			}

			wp_die( 'Webservice not found' );
		}
	}
}
