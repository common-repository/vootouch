<?php
class WTC_get_list_review {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_list_review
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
		add_action( 'wtc_get_list_review', array( $this, 'get_list_review' ) );
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
	
	public function get_list_review() {
		
		global $wpdb;

		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		global $product;

		$product_id = $postdata->product_id;
		$args = array ('post_type' => 'product', 'post_id' => $product_id,'status' => 'approve');
		$comments = get_comments( $args );
		$review_list = array();
		$i = 0;
		
		foreach($comments as $review){

			$rat = get_comment_meta( $review->comment_ID, 'rating', true );
			
			$d = "F j, Y";
			
			$comment_date = get_comment_date( $d, $review->comment_ID );
				
			$review_list['review'][$i]['review_ID'] = $review->comment_ID;
			$review_list['review'][$i]['review_post_ID'] = $review->comment_post_ID;
			$review_list['review'][$i]['review_author'] = $review->comment_author;
			$review_list['review'][$i]['review_author_email'] = $review->comment_author_email;
			$review_list['review'][$i]['review_date'] = $comment_date;
			$review_list['review'][$i]['review_content'] = $review->comment_content;
			$review_list['review'][$i]['review_user_id'] = $review->user_id;
			$review_list['review'][$i]['rating'] = $rat;
			$i++;
		}
		if(!$review_list){ 
			$review_list['msg'] = 'There are no reviews yet.';
			$review_list['first_review'] = 'Be the first to review';
		}

		header('Content-type: application/json');
		echo json_encode(array('data'=>$review_list));
	}
}
?>
