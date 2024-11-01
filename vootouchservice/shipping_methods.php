<?php
class WTC_get_shipping_methods{
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_shipping_methods
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
		add_action( 'wtc_get_shipping_methods', array( $this, 'get_shipping_methods' ) );
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
	
	public function get_shipping_methods() {
		
		global $wpdb, $woocommerce;
		 
		
		$postdata = file_get_contents("php://input");
		$postdata = json_decode($postdata);
				
		$active_methods = array();
		$shipping_zone    = WC_Shipping_Zones::get_zone_matching_package( $package );
		$shipping_methods = $shipping_zone->get_shipping_methods( true );
		$_taxobj = new WC_Tax();
		
		$country = $postdata->country;
		//exit;
		
		$state = $postdata->state;
		$postcode = $postdata->postcode;
		$city = $postdata->city;
		
		if(! $country){	$country = '*';		}
		if(! $state){	$state = '*';		}
		if(! $postcode){$postcode = '*';	}
		if(! $city){		$city = '*';			}
		
		$matched_tax_rates = array();
		
		$matched_tax_rates = $_taxobj->find_rates( array(
			'tax_id'   => array_keys($matched_tax_rates),
            'country'   => $country,
            'state'     => $state,
            'postcode'  => $postcode,
            'city'      => $city,
            'tax_class' => $tax_class,
            
        ) );
        
        $tx_id = array_keys($matched_tax_rates);
        $tax_id = $tx_id[0];
        
        foreach ($matched_tax_rates as $value){
			$value['tax_id'] = $tax_id;
			$matched_tax_rates = $value;
		}
        
        if(empty($matched_tax_rates)){
		$matched_tax_rates = array(
			'label' 	=> '0',
			'rate' 		=> '0',
			'shipping' 	=> '0',
			'compound' 	=> '0',
			'tax_id' 	=> '0'
		);
		}
		
		$result['taxes'] = $matched_tax_rates;
		
		$shipping_methods11 = $woocommerce->shipping->load_shipping_methods();
		
		$delivery_zones = WC_Shipping_Zones::get_zones();
		 
		foreach ( (array) $delivery_zones as $key => $the_zone ) {
		
 			  $zone_locations = $the_zone['zone_locations'][0]->code;					
			  $country;
			  	
			if($zone_locations == $country){
				  				
				  $res = 1;
				  break;
				  
			}else{
							
				$res = 0;
				
			}
		
		
		}

			 									
		if($res == '1'){
					
				foreach ( (array) $delivery_zones as $key => $the_zone ) {
 
				    $zone_locations = $the_zone['zone_locations'][0]->code;
 			  		
 			  		if($zone_locations == $country){
						
					$shipping_methods_new = $the_zone['shipping_methods'];
					
					foreach ($shipping_methods_new as $shipping_methods){
												
						if($shipping_methods->id != 'free_shipping'){
							
							$shipping_methods->min_amount ='';
							$shipping_methods->requires = '';
						}
						
										
						if ( isset( $shipping_methods->enabled ) && $shipping_methods->enabled	 == 'yes' ) {
													
							$active_methods[] = array( 'method_id' => $shipping_methods->id,'id' =>$shipping_methods->instance_id, 'title' => $shipping_methods->title , 'tax_status' => $shipping_methods->tax_status, 'cost' =>$shipping_methods->cost, 'min_amount' =>$shipping_methods->min_amount,'requires'=>$shipping_methods->requires);
	
						}	
					
				   }
			    }
			
			}
		}
				
		if($res == 0){	
	
		$zone                                                   = new \WC_Shipping_Zone( 0 );
		$zones[$zone->get_zone_id()]                            = $zone->get_data();
		$zones[$zone->get_zone_id()]['formatted_zone_location'] = $zone->get_formatted_location();
		$zones[$zone->get_zone_id()]['shipping_methods']        = $zone->get_shipping_methods();
			
		$shipping_methods11 = $zones[0]['shipping_methods'];
						
        foreach ( $shipping_methods11 as $id => $shipping_methods ) {
							
			if ( isset( $shipping_methods->enabled ) && $shipping_methods->enabled	 == 'yes' ) {
				if($shipping_methods->cost == NULL){
					$shipping_methods->cost ='';
				}
				if($shipping_methods->id != 'free_shipping'){
					$shipping_methods->min_amount ='';
					$shipping_methods->requires = '';
				}
				
				$active_methods[ $id ] = array( 'method_id' => $shipping_methods->id,'id' =>$shipping_methods->instance_id, 'title' => $shipping_methods->title , 'tax_status' => $shipping_methods->tax_status, 'cost' =>$shipping_methods->cost, 'min_amount' =>$shipping_methods->min_amount,'requires'=>$shipping_methods->requires);
							
			}
			
			
		}
			
	}
		

		
		$result['shipping_method'] = array_values($active_methods);
		
		
		
			$active_gateways = array();
			$gateways        = WC()->payment_gateways->payment_gateways();
		
			foreach ( $gateways as $id => $gateway ) {
			 
			  if ( isset( $gateway->enabled ) && $gateway->enabled == 'yes' ) {
				$enable = array();
				if($gateway->id == 'bacs' || $gateway->id == 'cod' || $gateway->id == 'cheque' || $gateway->id == 'paypal' || $gateway->id == 'payuindia' || $gateway->id == 'authorizenet'){
					if($gateway->enable_for_methods != NULL){
						$enable = $gateway->enable_for_methods;
					}else{
						$enable = array();
					}
					if($gateway->account_details != NULL){
						$ac = $gateway->account_details;
					}else{
						$ac = array();
					}
					
                                $description = $gateway->description;
                                if($gateway->id == 'authorizenet'){
                                   $description = $gateway->authorizenet_description;
                                }

				  $active_gateways[ $id ] = array( 'id' =>$gateway->id, 'title' => $gateway->title,'enabled'=>$gateway->enabled,'description' => $description, 'account_details' => $ac, 'enable_for_methods' => $enable,'instructions' => $gateway->instructions );
				}
			  }
			}
			
			$result['payment_gateways'] = array_values($active_gateways);
			
			 if(! $result){
				 $success = 0;
			 }else{
			 $success = 1;
			}

			header('Content-type: application/json');
			echo json_encode(array('success'=> $success,'data'=>$result));
		}
	}
?>
