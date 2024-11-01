<?php
class WTC_get_orderdetail {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_orderdetail
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
		add_action( 'wtc_get_orderdetail', array( $this, 'get_orderdetail' ) );
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
	
	public function get_orderdetail() {
		
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);

		$id = $postdata->orderid;
		
		if(! $id){
			header('Content-type: application/json');
			echo json_encode(array('success'=> 0,'count' =>0,'data'=>'Please Enter Order ID'));
		}
		$order = wc_get_order( $id );
		
		$order_post = get_post( $id );
		
		$order_data = array(
		  'order_id'                        => $order->id,
		  'order_number'              => $order->get_order_number(),
		  'status'                    => $order->get_status(),
		  'total'                     => wc_format_decimal( $order->get_total()),
		  'subtotal'                  => wc_format_decimal( $order->get_subtotal()),
		  'total_tax'                 => wc_format_decimal( $order->get_total_tax()),
		  'total_shipping'            => wc_format_decimal( $order->get_total_shipping()),
		  'cart_tax'                  => wc_format_decimal( $order->get_cart_tax()),
		  'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax()),
		  'total_discount'            => wc_format_decimal( $order->get_total_discount()),
		  'shipping_methods'          => $order->get_shipping_method(),
		  'payment_details' => array(
			'method_id'    => $order->payment_method,
			'method_title' => $order->payment_method_title,
			'paid'         => isset( $order->paid_date ),
		  ),
		  'billing_address' => array(
			'first_name' => $order->billing_first_name,
			'last_name'  => $order->billing_last_name,
			'company'    => $order->billing_company,
			'address_1'  => $order->billing_address_1,
			'address_2'  => $order->billing_address_2,
			'city'       => $order->billing_city,
			'state'      => $order->billing_state,
			'postcode'   => $order->billing_postcode,
			'country'    => $order->billing_country,
			'email'      => $order->billing_email,
			'phone'      => $order->billing_phone,
		  ),
		  'shipping_address' => array(
			'first_name' => $order->shipping_first_name,
			'last_name'  => $order->shipping_last_name,
			'company'    => $order->shipping_company,
			'address_1'  => $order->shipping_address_1,
			'address_2'  => $order->shipping_address_2,
			'city'       => $order->shipping_city,
			'state'      => $order->shipping_state,
			'postcode'   => $order->shipping_postcode,
			'country'    => $order->shipping_country,
		  ),
	);

	// Add line items.
    $count=count($order);
	$orderinfo=array();
    
		
		$date =  date_create($order_post->post_date_gmt);
		$order = new WC_Order($id);
		
		$orderitems=array(); $k=0;
		$billing_address = array();
		$shipping_address = array();
		$billing_address = array(
			'first_name' => $order->billing_first_name,
			'last_name'  => $order->billing_last_name,
			'company'    => $order->billing_company,
			'address_1'  => $order->billing_address_1,
			'address_2'  => $order->billing_address_2,
			'city'       => $order->billing_city,
			'state'      => $order->billing_state,
			'postcode'   => $order->billing_postcode,
			'country'    => $order->billing_country,
			'email'      => $order->billing_email,
			'phone'      => $order->billing_phone,
		  );
		  $shipping_address = array(
        'first_name' => $order->shipping_first_name,
        'last_name'  => $order->shipping_last_name,
        'company'    => $order->shipping_company,
        'address_1'  => $order->shipping_address_1,
        'address_2'  => $order->shipping_address_2,
        'city'       => $order->shipping_city,
        'state'      => $order->shipping_state,
        'postcode'   => $order->shipping_postcode,
        'country'    => $order->shipping_country,
      );
		$orderinfo['order_number']= $order->get_order_number();
		$orderinfo['date'] =   date_format($date,"Y-m-d");
		$orderinfo['status']= $order->get_status();
		$orderinfo['billing_address'] = $billing_address;
		$orderinfo['shipping_address'] = $shipping_address;
		$orderinfo['payment_method_title'] = $order->payment_method_title;
		$orderinfo['total'] = wc_format_decimal( $order->get_total());
		$orderinfo['subtotal'] = wc_format_decimal( $order->get_subtotal());
		$orderinfo['total_discount'] = wc_format_decimal( $order->get_total_discount());
		$orderinfo['shipping_methods'] = $order->get_shipping_method();
		
		foreach ($order->get_items() as $key => $lineItem) {
						
				$orderitems[$k]['itemid']= $lineItem['product_id'];
				$orderitems[$k]['qty']= $lineItem['qty'];
				$orderitems[$k]['name']= $lineItem['name'];
				$orderitems[$k]['price'] = $lineItem['line_subtotal'];
				
				$k++;
		}
		
		$orderinfo =  array("products" => $orderitems);
		$orderinfo['order_detail'] = $order_data;

		$orders=array("orders_detail" => $orderinfo);
		
		header('Content-type: application/json');
		echo json_encode(array('success'=> 1,'count' =>$count,'data'=>$orders));
	}
}
?>
