<?php  
class WTC_get_category {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_category
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
		add_action( 'wtc_get_category', array( $this, 'get_category' ) );
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
	
	public function get_category() {
	
	
	$terms = get_categories( array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'parent' => 0,
	) );
	$success=0;

	$categories=array();
	
	for($i=0;$i<count($terms);$i++)
	{   
		$success=1;
		
		//if($terms[$i]->count >= 1){
			
		$categories[$i]['category_id'] = $terms[$i]->term_id;
		$categories[$i]['category_name'] = html_entity_decode($terms[$i]->name);
		$categories[$i]['count'] = $terms[$i]->count;

		$children = get_term_children($terms[$i]->term_id, 'product_cat'); // get children

		if(sizeof($children)==0)
		$categories[$i]['child'] = 0;
		if(sizeof($children)>0)
		$categories[$i]['child'] = 1;
		
		$categories[$i]['description'] = strip_tags($terms[$i]->description);
		$categories[$i]['slug'] = $terms[$i]->slug;
		
		$thumbnail_id = get_woocommerce_term_meta( $terms[$i]->term_id, 'thumbnail_id', true );
		if($thumbnail_id)
			$categories[$i]['image'] = wp_get_attachment_url( $thumbnail_id );
		else
		 $categories[$i]['image'] =wc_placeholder_img_src();  
		 
	  //}
	
	}
			
	$categories=array("product_categories" => $categories);
	
	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'data'=>$categories));
	}
}
?>
