<?php
class WTC_get_save_order {
	private static $instance = null;
	
	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_save_order
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
		add_action( 'wtc_get_save_order', array( $this, 'get_save_order' ) );
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
	
	public function get_save_order() {
	
	global $wpdb,$woocommerce;
	
	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);
	
	$code = $postdata->coupon_code;

	/*********** TAX ************/
	$tax_rate_id= $postdata->tax_id;
	$tax_obj = WC_Tax::_get_tax_rate( $tax_rate_id );
	$tx_rate = $tax_obj['tax_rate'];
	$ratecode = WC_Tax::get_rate_code( $tax_rate_id );
	$shipping_tax_amount = $postdata->ship_tax;
	/*********** End TAX ********/

	$use_billing_address = $postdata->use_billing_address;
	$success=0;  
	
	$user =$postdata->user_id;

	$user_id = get_current_user_id(); 
	$user_info = get_userdata($user_id);

	$shipping_method = $postdata->shipp_method;
	$shipping_id = $postdata->ship_method_id.':'.$postdata->ship_id;
	$shipping_rate_taxes = $postdata->ship_tax;
	$shipping_cost = $postdata->ship_amount;
	
    //get a billing data
    
	if($user_id == 0){
		$billing_first_name =  $postdata->billing_first_name;
		$billing_last_name = $postdata->billing_last_name;
		$billing_company = $postdata->billing_company;
		$billing_email = $postdata->billing_email;
		$billing_phone = $postdata->billing_phone;
		$billing_country = $postdata->billing_country;
		$billing_address_1 = $postdata->billing_address_1;
		$billing_city = $postdata->billing_city;
		$billing_state = $postdata->billing_state;
		$billing_postcode = $postdata->billing_postcode;
	}else{
		$billing_first_name = get_user_meta($user, 'billing_first_name', true);
		$billing_last_name = get_user_meta($user, 'billing_last_name', true);
		$billing_company = get_user_meta($user, 'billing_company', true);
		$billing_email = get_user_meta($user, 'billing_email', true);
		$billing_phone = get_user_meta($user, 'billing_phone', true);
		$billing_country = get_user_meta($user, 'billing_country', true);
		$billing_address_1 = get_user_meta($user, 'billing_address_1', true);
		$billing_address_2 = get_user_meta($user, 'billing_address_2', true);
		$billing_city = get_user_meta($user, 'billing_city', true);
		$billing_state = get_user_meta($user, 'billing_state', true);
		$billing_postcode = get_user_meta($user, 'billing_postcode', true);
	}
 
	if($use_billing_address == 0){
	
	
		if($user_id == 0){
			$shipping_first_name = $postdata->shipping_first_name;
			$shipping_last_name = $postdata->shipping_last_name;
			$shipping_company = $postdata->shipping_company;
			$shipping_country = $postdata->shipping_country;
			$shipping_address_1 = $postdata->shipping_address_1;
			//$shipping_address_2 = $postdata->user_id;
			$shipping_city = $postdata->shipping_city;
			$shipping_state = $postdata->shipping_state;
			$shipping_postcode = $postdata->shipping_postcode;
		}else{
			$shipping_first_name = get_user_meta($user, 'shipping_first_name', true);
			$shipping_last_name = get_user_meta($user, 'shipping_last_name', true);
			$shipping_company = get_user_meta($user, 'shipping_company', true);
			$shipping_country =get_user_meta($user, 'shipping_country', true);
			$shipping_address_1 =get_user_meta($user, 'shipping_address_1', true);
			$shipping_address_2 = get_user_meta($user, 'shipping_address_2', true);
			$shipping_city = get_user_meta($user, 'shipping_city', true);
			$shipping_state = get_user_meta($user, 'shipping_state', true);
			$shipping_postcode = get_user_meta($user, 'shipping_postcode', true);
		}
		
		//get a shipping data 
   
    }else{
	 
		$shipping_first_name = $billing_first_name;
		$shipping_last_name = $billing_last_name;
		$shipping_company = $billing_company;
		$shipping_email = $billing_email ;
		$shipping_phone =$billing_phone;
		$shipping_country =$billing_country;
		$shipping_address_1 =$billing_address_1;
		$shipping_address_2 =$billing_address_2;
		$shipping_city = $billing_city ;
		$shipping_state = $billing_state;
		$shipping_postcode = $billing_postcode;
	}
	
	$product_count = count($postdata->postdata);
		
    if($product_count >= 1){
  	//get order id
	$order_id = WC()->checkout()->create_order();
	
	$payment_method_title = $postdata->payment_method;
	$payment_payment_id = $postdata->payment_id;
	
	update_post_meta( $order_id, "_customer_user", $user );	
	update_post_meta( $order_id, "_order_shipping", $shipping_cost );
	update_post_meta( $order_id, "_order_shipping_tax", $shipping_rate_taxes );
	
	update_post_meta( $order_id, "_payment_method", $payment_payment_id );
	update_post_meta( $order_id, "_payment_method_title", $payment_method_title );
			
	//billing data	
	update_post_meta( $order_id, "_billing_first_name", $billing_first_name );
	update_post_meta( $order_id, "_billing_last_name", $billing_last_name );
	update_post_meta( $order_id, "_billing_company", $billing_company );
	update_post_meta( $order_id, "_billing_email", $billing_email );
	update_post_meta( $order_id, "_billing_phone", $billing_phone );
	update_post_meta( $order_id, "_billing_country", $billing_country );
	update_post_meta( $order_id, "_billing_address_1", $billing_address_1 );
	update_post_meta( $order_id, "_billing_address_2", $billing_address_2 );
	update_post_meta( $order_id, "_billing_city", $billing_city );
	update_post_meta( $order_id, "_billing_state", $billing_state );
	update_post_meta( $order_id, "_billing_postcode", $billing_postcode );

	//shipping data
	update_post_meta( $order_id, "_shipping_first_name", $shipping_first_name );
	update_post_meta( $order_id, "_shipping_last_name", $shipping_last_name );
	update_post_meta( $order_id, "_shipping_company", $shipping_company );
	update_post_meta( $order_id, "_shipping_email", $shipping_email );
	update_post_meta( $order_id, "_shipping_phone", $shipping_phone );
	update_post_meta( $order_id, "_shipping_country", $shipping_country );
	update_post_meta( $order_id, "_shipping_address_1", $shipping_address_1 );
	update_post_meta( $order_id, "_shipping_address_2", $shipping_address_2 );
	update_post_meta( $order_id, "_shipping_city", $shipping_city );
	update_post_meta( $order_id, "_shipping_state", $shipping_state );
	update_post_meta( $order_id, "_shipping_postcode", $shipping_postcode );
	
	if($code){
		$item_id1 = wc_add_order_item( $order_id, array(
			'order_item_name' => $code,
			'order_item_type' => 'coupon'
		));
	}
	
	$item_id3 = wc_add_order_item( $order_id, array(
			'order_item_name' 		=> $shipping_method,
			'order_item_type' 		=> 'shipping'
	) );
	
	if($ratecode){
			$item_id2 = wc_add_order_item( $order_id, array(
				'order_item_name' => $ratecode,
				'order_item_type' => 'tax'
			) );
		}
	/******************************************/
	update_metadata( 'order_item', $item_id3, 'method_id', $shipping_id );
	update_metadata( 'order_item', $item_id3, 'cost', wc_format_decimal( $shipping_cost ) );
	$taxeses              = array();
	
	update_metadata( 'order_item', $item_id3, 'taxes', array('a:1:{i:1;s:1:',$shipping_rate_taxes) );
	
	foreach ( $postdata->postdata as $val) {
		$pro_id = $val->product_id;
		$pro_qty = $val->qty;
		$pro_name = get_the_title( $pro_id );
		
		$product1 = new WC_Product( $pro_id );
		$pro_price = $product1->price;
		$tot =  $pro_price * $pro_qty;
		$order_tt += $tot;
		$items_in_package[] = $product1->get_title() . ' &times; ' . $pro_qty;
	}
	update_metadata( 'order_item', $item_id3, 'Items', implode( ', ', $items_in_package ) );
	
		
	foreach($postdata->postdata as $value)
	{
		
		$product_id = $value->product_id;
		
		if($value->variation_id){
		
		 $variation_id = $value->variation_id;		   
		
		}
		
		
		
		$qty = $value->qty;
		$product_name = get_the_title( $product_id );
		
		$product = new WC_Product( $product_id );
		
		$product_price = $product->price;
		
		//Edit by dev varition price
		if($value->variation_id){
				
		$product_variation = new WC_Product_Variation($variation_id);
		
		$product_price = $product_variation->regular_price;
	
		}
		//Edit End 
		
		$total =  $product_price * $qty;
		
        $order_total += $total;
        
		$item_id = wc_add_order_item( $order_id, array(
			'order_item_name' => $product_name,
			'order_item_type' => 'line_item',
		));
			
		$coupon_type = $postdata->coupon_type;
		$coupon_amount = $postdata->coupon_amount;
		
		
		if ( $coupon_type == 'percent_product') {
			$discount_amount = $coupon_amount * ( $total / 100 );
			$coupon_amount1 += $discount_amount;
		}
		if ( $coupon_type == 'percent') {
			
			$discount_amount = $coupon_amount * ( $total / 100 );
			$coupon_amount1 += $discount_amount;
		}
		if ( $coupon_type == 'fixed_cart') {
			$discount_percent = ( $product->get_price_excluding_tax() * $qty ) / $order_tt /*Sub Total Ex*/; 
			
			$discount_amount = ( $coupon_amount * $discount_percent ) / $qty; 
			$coupon_amount1 = $coupon_amount;
		}
		
		if ( $coupon_type == 'fixed_product' ) {
			$discount_amount = min( $coupon_amount, $total );
			$discount_amount = $discount_amount * $qty;
			$coupon_amount1 += $discount_amount;
		}
		
		if($discount_amount){
			$line_tot = $total - $discount_amount;
			if($product->is_taxable()){
				$tax_amount = $line_tot * $tx_rate / 100;
				$tax_sub_total = $total * $tx_rate / 100;
				$ftax += $tax_amount;
			}else{
				$tax_amount=0;
				$tax_sub_total = 0;
			}
			$discount_amount_tax = $discount_amount * $tx_rate / 100;
			$ord_tt += $total - $discount_amount + $tax_amount;
			$order_tot =  $ord_tt + $shipping_cost + $shipping_rate_taxes; 
			
		}else{
			$line_tot = $total;
			$tax_amount = $line_tot * $tx_rate / 100;
			$tax_sub_total = $tax_amount;
			$ftax += $tax_amount;
			$discount_amount_tax = 0;
			$ord_tt += $total + $tax_amount;
			$order_tot =  $ord_tt + $shipping_cost + $shipping_rate_taxes; 
		}
		
		/***************** tax **********************/
		
		if($ratecode){
		update_metadata( 'order_item', $item_id2, 'rate_id', $tax_rate_id );
		update_metadata( 'order_item', $item_id2, 'label', WC_Tax::get_rate_label( $tax_rate_id ) );
		update_metadata( 'order_item', $item_id2, 'compound', WC_Tax::is_compound( $tax_rate_id ) ? 1 : 0 );
		update_metadata( 'order_item', $item_id2, 'tax_amount', wc_format_decimal($ftax));
		update_metadata( 'order_item', $item_id2, 'shipping_tax_amount',  wc_format_decimal($shipping_tax_amount));
		}
		/************* End  tax **********************/
		update_metadata( 'order_item', $item_id, '_qty', $qty, '' );
		update_metadata( 'order_item', $item_id, '_product_id', $product_id, '' );
		update_metadata( 'order_item', $item_id, '_line_subtotal',  $total, '' );
		
		//Edit by dev
		if($value->variation_id){
			
			update_metadata( 'order_item', $item_id, '_variation_id', $variation_id );
			$variation = wc_get_product($variation_id);
			$attributes = $variation->get_variation_attributes();
			foreach($attributes as $variant => $variant_value){
				wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $variant ), $variant_value );
			}
		}
		//Edit end
		
		
		update_metadata( 'order_item', $item_id, '_line_total', $line_tot );
		update_metadata( 'order_item', $item_id, '_line_subtotal_tax', wc_format_decimal($tax_sub_total) );
		update_metadata( 'order_item', $item_id, '_line_tax', wc_format_decimal($tax_amount) );
		
		$line_subtotal_tax = array();
		$line_tax = array();
		
		$line_subtotal_tax   =  wc_format_decimal($tax_sub_total);
		$line_tax            =  wc_format_decimal($tax_amount);
				
		wc_update_order_item_meta( $item_id,'_line_tax_data', array('total' => array('i:1',$line_tax), 'subtotal' => array('i:1',$line_subtotal_tax)));
		
		/**********************************************************************************/
		if($code){
		update_metadata( 'order_item', $item_id1, 'discount_amount', $discount_amount );
		update_metadata( 'order_item', $item_id1, 'discount_amount_tax', $discount_amount_tax );
		}
		update_post_meta( $order_id, '_cart_discount', $coupon_amount1 );
		update_post_meta( $order_id, '_cart_discount_tax', $discount_amount_tax );
		update_post_meta( $order_id, "_order_total", $order_tot);
	}
	
	//order status data    
    $order = new WC_Order( $order_id );
    if($payment_payment_id == 'bacs' || $payment_payment_id == 'cheque' || $payment_payment_id == 'paypal'){
		$order->update_status( 'on-hold' );
	}
/*
	else{
		$order->update_status( 'processing' );
	}	
      */  
	//Edit Authorize.net code	
	//$payment_payment_id = 'authorize';	
	if($payment_id == 'authorizenet'){
	
		$address = $billing_address_1.' '.$billing_city;
				
		//sandbox url		
		$post_url = "https://test.authorize.net/gateway/transact.dll";
		//live url
		//$post_url = "https://secure.authorize.net/gateway/transact.dll";
			
		$post_values = array(
			
			//the API Login ID and Transaction Key must be replaced with valid values
			"x_login"			=> "9Law93D9",
			"x_tran_key"		=> "4E5mYr4w63Dt52wg",

			"x_version"			=> "3.1",
			"x_delim_data"		=> "TRUE",
			"x_delim_char"		=> "|",
			"x_relay_response"	=> "FALSE",
			
			"x_type"			=> "AUTH_CAPTURE",
			"x_method"			=> "CC",
			"x_card_num"		=> $card_num,
			"x_exp_date"		=> $exp_date,

			"x_amount"			=> $pay_amount,
			"x_invoice_num"		=> $order_id,	
			"x_description"		=> "Order #".$order_id,

			"x_first_name"		=> $billing_first_name,
			"x_last_name"		=> $billing_last_name,
			"x_address"			=> $address,
			"x_state"			=> $billing_state,
			"X_status" 			=> "completed",
			"x_zip"				=> $billing_postcode
			// Additional fields can be added here as outlined in the AIM integration
			// guide at: http://developer.authorize.net
					
		);
		
		$post_string = "";
		foreach( $post_values as $key => $value )
		{ 
			$post_string .= "$key=" . urlencode( $value ) . "&"; 
			
		}
		$post_string = rtrim( $post_string, "& " );
		$request = curl_init($post_url);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
		$post_response = curl_exec($request);
		curl_close ($request);
		
		$response_array = explode($post_values["x_delim_char"],$post_response);	
		
		$response_array[2];
				
		if ($response_array[2] == 1) {
			
			$order = new WC_Order( $order_id );
		    $order->update_status( 'completed' );			
			
		}else{
			
			$order = new WC_Order( $order_id );					 			 
			$order->update_status( 'pending' );
		}		
	
	}
	//Edit end
	 	$action = "send_email_customer_processing_order";
	 
				do_action( 'woocommerce_before_resend_order_emails', $order);

				// Ensure gateways are loaded in case they need to insert data into the emails
				WC()->payment_gateways();
				WC()->shipping();

				// Load mailer
				$mailer = WC()->mailer();

				$email_to_send = str_replace( 'send_email_', '', $action );

				$mails = $mailer->get_emails();

				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( $mail->id == $email_to_send ) {
							$mail->trigger( $order->id );
							$order->add_order_note( sprintf( __( '%s email notification manually sent.', 'woocommerce' ), $mail->title ), false, true );
						}
					}
				}

				do_action( 'woocommerce_after_resend_order_email', $order, $email_to_send );

				// Change the post saved message
				
	 
	$success=1;		
	}

header('Content-type: application/json');
echo json_encode(array('success'=> $success,'data'=>$result));
}
}
?>
