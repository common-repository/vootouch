<?php
class WTC_get_save_review {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_save_review
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
		add_action( 'wtc_get_save_review', array( $this, 'get_save_review' ) );
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
	
	public function get_save_review() {
		
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		global $product,$wpdb;
		$user = wp_get_current_user();
			
		$comment_author       = $postdata->comment_author;
		$comment_author_email = $postdata->comment_author_email;
		$comment_author_url   = 'http://';
		$user_ID              = $postdata->user_id;

	$time = current_time('mysql');

	$post_id = $postdata->comment_post_id;

	$data = array(
		'comment_post_ID' =>  $post_id,
		'comment_author' => $comment_author,
		'comment_author_email' =>  $comment_author_email,
		'comment_author_url' => 'http://',
		'comment_content' =>  $postdata->comment_content,
		'comment_type' => '',
		'comment_parent' => 0,
		'user_id' =>  $user_ID,
		'comment_author_IP' => '127.0.0.1',
		'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
		'comment_date' => $time,
		'comment_approved' =>  0,
	);


	$comment_id  = wp_insert_comment($data);


	add_comment_meta($comment_id , 'rating', (int) esc_attr( $postdata->rating ), true );


		$success=1;
		$result['msg'] = 'Comment Insert Successfully.';

	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$result));	
	}
}
?>
