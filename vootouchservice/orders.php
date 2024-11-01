<?php  
class WTC_get_orders {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_orders
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
		add_action( 'wtc_get_orders', array( $this, 'get_orders' ) );
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
	
	public function get_orders() {
	
	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);
	$userid=$postdata->user_id;
    
   $orders = get_posts( array(
    'numberposts' => -1,
    'meta_key'    => '_customer_user',
    'meta_value'  => $userid,
    'post_type'   => wc_get_order_types(),
    'post_status' => array_keys( wc_get_order_statuses() ),
	) );
	
	$count=count($orders);
	$orderinfo=array();   
    for($i=0;$i<count($orders);$i++)
    { 
		$date=date_create($orders[$i]->post_date);
		$order = new WC_Order($orders[$i]->ID);
		$total = $order->get_total();
		$orderinfo[$i]['orderid'] = $orders[$i]->ID;
		$orderinfo[$i]['title'] = $orders[$i]->post_title;
		$orderinfo[$i]['status'] = $orders[$i]->post_status;
		$orderinfo[$i]['date'] =   date_format($date,"Y-m-d");
		$orderinfo[$i]['time'] =   date_format($date,"H:i:s");
		$orderinfo[$i]['total'] = $total;
		$orderitems=array(); $k=0;
		
		foreach ($order->get_items() as $key => $lineItem) {
			$orderitems[$k]['itemid']= $lineItem['product_id'];
			$orderitems[$k]['name']= $lineItem['name'];
			$orderitems[$k]['qty']= $lineItem['qty'];
			$orderitems[$k]['subtotal']= $lineItem['line_subtotal'];
			$orderitems[$k]['total']= $lineItem['line_total'];
			
			$k++;
		}
    $orderinfo[$i]['items'] =  $orderitems;
	}
	
	
	if(! empty($orders)){
		$success = 1;
	}else{
		$success = 0;
	}
	$orders=array("orders" => $orderinfo);
	
	header('Content-type: application/json');
	echo json_encode(array('success'=> $success,'count' =>$count,'data'=>$orders));
	}
}
?>
