<?php
class WTC_Output {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_Output
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
	}

	/**
	 * The correct way to output data in a webservice call
	 *
	 * @param $data
	 */
	public function output( $data ) {
		$data = apply_filters( 'wtc_output_data', $data );
		echo json_encode( $data );
	}
}
