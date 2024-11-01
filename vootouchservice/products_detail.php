<?php  
class WTC_get_products_detail {
	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_products_detail
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
		//add_action( 'wpsws_webservice_get_posts', array( $this, 'get_posts' ) );
		add_action( 'wtc_get_products_detail', array( $this, 'get_products_detail' ) );
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
	
	public function get_products_detail() {
	
	global $wpdb ,$woocommerce;
		
	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);
	
	
	$catslug=$postdata->catslug;
	$theid = $postdata->theid;
	/*
	$theid = 3583;
	
	$country = 'IN';
	$state = 'GU';
	$postcode = '38009';
	$city = 'AHMEDABAD';
	*/
		
	$country = $postdata->country;
	$state = $postdata->state;
	$postcode = $postdata->postcode;	
	$city = $postdata->city;
	
	
	if(!$theid){
		header('Content-type: application/json');
		echo json_encode(array('success'=> 0, 'data'=>'No Products Found'));
		exit;
	}
	
		if($postdata->search)
		$search=$postdata->search;

		$taxquery="";
		if($catslug)
		{
			$taxquery = array(
			array(
				'taxonomy'  => 'product_cat',
				'field'     => 'slug', 
				'terms'     => $catslug
			)
		) ;
		}
	 
		$s="";
		if($search)
		{
			$s=  $search ;
		}
	 
	  $args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        =>  array('product'),
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'tax_query' => array(
			array(
				'taxonomy'  => 'product_cat',
				'field'     => 'slug', 
				'terms'     => 'albums'
			)
		),
		 
		'post_status'      => 'publish' 
	 );

	//$wq = new WP_Query( array( 'post_type' => array('product'),  's' => $s, 'posts_per_page' => -1,  
	//'tax_query'     => $taxquery) );
    
		$products=array();
		//if ( $wq->have_posts() ) {  
			//while ( $wq->have_posts() ) : $wq->the_post();
		$product_detail['id'] = $theid ;
		$product= new WC_Product($theid); 
		$product1   = wc_get_product( $theid);
		$product_detail['title']= $product->post->post_title;
		$product_detail['qty']= "";
		$product_detail['price'] = $product->price;
		$long_desc =  strip_tags($product->post->post_content);
		$short_desc = strip_tags($product->post->post_excerpt);
		if(empty($short_desc)){
			$product_detail['description'] = $long_desc;
		}else{
			$product_detail['description'] = $short_desc;
		}
		$product_detail['short_description'] = strip_tags($product->post->post_excerpt);
		$product_detail['status']= $product->post->post_status;
		$product_detail['rating'] = strip_tags($product->get_average_rating()); 
		$product_detail['review'] = $product->get_review_count();
		$product_detail['display_review'] = $product->post->comment_status;
		$product_detail['currency_symbol']= html_entity_decode(get_woocommerce_currency_symbol());
		$product_detail['decimal_separator']  = wc_get_price_decimal_separator();
		$product_detail['thousand_separator'] = wc_get_price_thousand_separator();
		$product_detail['decimals']           = wc_get_price_decimals();
		$product_detail['price_format']       = get_woocommerce_price_format();
		$product_detail['product_type']=  $product1->product_type;
		$product_detail['tax_status'] = $product->get_tax_status();
		$product_detail['weight'] = $product->get_weight();
		$product_detail['dimensions'] = $product->get_dimensions();
		
		if ( wc_tax_enabled()){
			$product_detail['suffix'] = strip_tags($product->get_price_suffix());
		}
		
		if($product1->is_type( 'simple' ) || $product1->is_type( 'external' ) || $product1->is_type( 'grouped' )){

			$product_detail['min_qty'] = 1;
			$product_detail['max_qty'] = $product1->get_stock_quantity();
			$availability = $product->get_availability();
			$product_detail['is_in_stock'] = $product1->is_in_stock();
			$product_detail['availability_html'] = $availability['availability'];
			//$product_detail['origional_price'] = $product->get_price_excluding_tax();
			//$product_detail['origional_price'] = $product->price;
			
			//Edit by dev						
			if( 'yes' == get_option( 'woocommerce_prices_include_tax' ) && 'incl' != get_option( 'woocommerce_tax_display_cart' )){
				
				 $product_detail['origional_price'] = $product->get_price_excluding_tax();
			
			}else{
				
				$product_detail['origional_price'] = $product->price;  
			}
			
			if ( $product->is_on_sale() ){
				$product_detail['on_sale'] = "Sale!";
		    	}else{
				$product_detail['on_sale'] = "";
			}
	    	
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ! wc_prices_include_tax() ) {
				//$product_detail['cart_sale_price'] = $product->get_price_including_tax();
				
				if( 'yes' == get_option( 'woocommerce_prices_include_tax' )){
					
					$product_detail['cart_sale_price'] = $product->get_price_excluding_tax();
									
				}else{
					
					$product_detail['cart_sale_price'] = $product->get_price_including_tax();
				}
				
			}else{
				//$product_detail['cart_sale_price'] = $product->price;
				
				if( 'yes' == get_option( 'woocommerce_prices_include_tax') && 'incl' != get_option( 'woocommerce_tax_display_cart' )){
					
					$product_detail['cart_sale_price'] = $product->get_price_excluding_tax();
									
				}else{
					
					$product_detail['cart_sale_price'] = $product->price;
				}
				
			}
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				
				$sale_pri = html_entity_decode(strip_tags(wc_price($product->get_price_including_tax())));
				$reg_pri = html_entity_decode(strip_tags(wc_price($product->get_display_price($product->get_regular_price()))));
				if($sale_pri == $reg_pri ){
					$product_detail['sale_price'] = '';
					$product_detail['regular_price'] = $reg_pri;
				}else{
					$product_detail['sale_price'] = $sale_pri;
					$product_detail['regular_price'] = $reg_pri;
				}
			}else{
				$sale_pp = $product->get_sale_price();
				if(empty($sale_pp)){
					$sale_pp = $product->get_sale_price();
				}else{
					$sale_pp = html_entity_decode(strip_tags(wc_price($product->get_sale_price())));
				}
				
				if( 'yes' == get_option( 'woocommerce_prices_include_tax' )){
					
					if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) &&  wc_prices_include_tax() ) {
															
						$product_detail['sale_price'] = $sale_pp;
						$product_detail['regular_price'] = html_entity_decode(strip_tags(wc_price($product->get_regular_price()))); 
		
					}else{
						
					$_taxobj = new WC_Tax();
			
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
					
					if(empty($sale_pp)){
						
						$product_detail['sale_price'] ="";
					}else{		
					
						$product_detail['sale_price'] = html_entity_decode(strip_tags(wc_price($product->get_price_excluding_tax())));
					
					}
					
					$tax_price_regular = $product->get_regular_price();
					$tax_rat_regular = $matched_tax_rates['rate'];
						 
					$p =  $product->get_regular_price();
														
					$t =  $p * 100 / ($tax_rat_regular + 100);
					
					$product_detail['regular_price'] = html_entity_decode(strip_tags(wc_price($t)));
					
				 }
				}else{
				
					$product_detail['sale_price'] = $sale_pp;
					$product_detail['regular_price'] = html_entity_decode(strip_tags(wc_price($product->get_regular_price())));
					
				}
				
				$grouped_products = $product1->get_children();
				
				
				if($product1->is_type( 'grouped' ) ){
				
				foreach ( $grouped_products as $child_id ) {
					
					$all_prices[] = get_post_meta( $child_id, '_price', true );
					
				}

                              if ( $product1->is_on_sale() ){
				$product_detail['on_sale'] = "Sale!";
		    	       }else{
				$product_detail['on_sale'] = "";
			      }

				$max = max($all_prices);
                                $min = min($all_prices);

                                $product_detail['origional_price'] = $min;                               
                                $product_detail['regular_price'] = html_entity_decode(strip_tags(wc_price($max)));
                                $product_detail['sale_price'] = html_entity_decode(strip_tags(wc_price($min)));
                                $product_detail['cart_sale_price'] = $min;
				
			     }	
			
		 }
			
			
						
		 //Edit by dev
		 if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() && $product->is_taxable()) {
						 			
			$_taxobj = new WC_Tax();
			
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
		
			$products[$i]['taxes'] = $matched_tax_rates;
		
		
			//regular_price
			$tax_price_regular = $product->get_regular_price();
			$tax_rat_regular = $matched_tax_rates['rate'];
			$count_tax_regular = $tax_price_regular * $tax_rat_regular / 100;
			$product_with_price_regular = $tax_price_regular + $count_tax_regular;		
			
			$product_detail['tax_product_with_price_regula'] = html_entity_decode(strip_tags(wc_price($product_with_price_regular))); 
           
           //sale_price        
           $tax_price_sale = $product->get_sale_price(); 
           
           if($tax_price_sale){
			   	         
            $tax_rat_sale = $matched_tax_rates['rate'];
			$count_tax_sale = $tax_price_sale * $tax_rat_sale / 100;
			$product_with_price_sale = $tax_price_sale + $count_tax_sale;		
			$product_detail['tax_product_with_price_sale'] = html_entity_decode(strip_tags(wc_price($product_with_price_sale))); 
			
			}else{
				
				$product_detail['tax_product_with_price_sale'] = "";
				
			 }
			
		  }else{
			
			$product_detail['tax_product_with_price_regula'] = "";
			$product_detail['tax_product_with_price_sale'] = "";
			
			
		  }    
		  
		  if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ! wc_prices_include_tax() && $product1->is_taxable()) {
			
				if($product_detail['cart_sale_price']){

					$tax_price_min = $product_detail['cart_sale_price'];							
					$tax_rat_min = $matched_tax_rates['rate'];
					$count_tax_min = $tax_price_min * $tax_rat_min / 100;				
					$product_detail['cart_sale_price'] = $tax_price_min + $count_tax_min;
										
				}
		 }		 
         //Edit End

		}
		if($product1->is_type( 'external' ) ){
				
		        if ( $product1->product_url ){
					$product_detail['product_url'] = $product1->product_url;
					$product_detail['button_text'] = $product1->button_text;
	    		}else{
					$product_detail['product_url'] = "";
				}
			}
		if($product1->is_type( 'grouped' ) ){
		
			$grouped_products = $product1->get_children();
				
			$child = array();
				 
			if ( $grouped_products ){
					
				foreach ($grouped_products as $child_id) {
					
					$product = new WC_Product($child_id);
					$product1 = wc_get_product($child_id); 
						
					$child['product_id'] = $child_id;
						
						$child['title']= html_entity_decode($product->name);
						
						$child['qty']= "";
						$child['price'] = $product->price;
						$long_desc =  strip_tags($product->post->post_content);
						$short_desc = strip_tags($product->post->post_excerpt);
						if(empty($short_desc)){
							$child['description'] = $long_desc;
						}else{
							$child['description'] = $short_desc;
						}
						$child['short_description'] = strip_tags($product->post_excerpt);
						$child['status']= $product->post->post_status;
						$child['rating'] = strip_tags($product->get_average_rating()); 
						$child['review'] = $product->get_review_count();
						$child['display_review'] = $product->post->comment_status;
						$child['currency_symbol']= html_entity_decode(get_woocommerce_currency_symbol());
						$child['decimal_separator']  = wc_get_price_decimal_separator();
						$child['thousand_separator'] = wc_get_price_thousand_separator();
						$child['decimals']           = wc_get_price_decimals();
						$child['price_format']       = get_woocommerce_price_format();
						$child['product_type']=  $product1->product_type;
						$child['tax_status'] = $product->get_tax_status();
						$child['weight'] = $product->get_weight();
						$child['dimensions'] = $product->get_dimensions();
			
						$child['min_qty'] = 1;
						$child['max_qty'] = $product->get_stock_quantity();
						$availability = $product->get_availability();
						$child['is_in_stock'] = $product->is_in_stock();
						$child['availability_html'] = $availability['availability'];
						//$product_detail['origional_price'] = $product->get_price_excluding_tax();
						//$product_detail['origional_price'] = $product->price;
			
						//Edit by dev						
						if( 'yes' == get_option( 'woocommerce_prices_include_tax' ) && 'incl' != get_option( 'woocommerce_tax_display_cart' )){
				
							$child['origional_price'] = $product->get_price_excluding_tax();
			
						}else{
				
							$child['origional_price'] = $product->price;  
						}
			
						if ( $product->is_on_sale() ){
							$child['on_sale'] = "Sale!";
						}else{
							$child['on_sale'] = "";
						}
	    	
						if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ! wc_prices_include_tax() ) {
						//$product_detail['cart_sale_price'] = $product->get_price_including_tax();
				
							if( 'yes' == get_option( 'woocommerce_prices_include_tax' )){
					
								$child['cart_sale_price'] = $product->get_price_excluding_tax();
									
							}else{
					
								$child['cart_sale_price'] = $product->get_price_including_tax();
							}
				
						}else{
						//$product_detail['cart_sale_price'] = $product->price;
				
							if( 'yes' == get_option( 'woocommerce_prices_include_tax') && 'incl' != get_option( 'woocommerce_tax_display_cart' )){
					
								$child['cart_sale_price'] = $product->get_price_excluding_tax();
									
							}else{
					
								$child['cart_sale_price'] = $product->price;
							}
				
						}
					if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				
							$sale_pri = html_entity_decode(strip_tags(wc_price($product->get_price_including_tax())));
							$reg_pri = html_entity_decode(strip_tags(wc_price($product->get_display_price($product->get_regular_price()))));
						if($sale_pri == $reg_pri ){
							$child['sale_price'] = '';
							$child['regular_price'] = $reg_pri;
						}else{
							$child['sale_price'] = $sale_pri;
							$child['regular_price'] = $reg_pri;
						}
					}else{
						$sale_pp = $product->get_sale_price();
						if(empty($sale_pp)){
							$sale_pp = $product->get_sale_price();
						}else{
							$sale_pp = html_entity_decode(strip_tags(wc_price($product->get_sale_price())));
						}
				
						if( 'yes' == get_option( 'woocommerce_prices_include_tax' )){
					
							if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) &&  wc_prices_include_tax() ) {
											
								$child['sale_price'] = $sale_pp;
			
					$child['regular_price'] = html_entity_decode(strip_tags(wc_price($product->get_regular_price()))); 



$product_vra = new WC_Product($child_id);

$product_vra = wc_get_product($child_id); 

$child['regular_price'];

if($product_vra->is_type( 'variable' ) ){

$child['regular_price'] = html_entity_decode(strip_tags(wc_price($child[origional_price]))); 


}


							}else{
						
								$_taxobj = new WC_Tax();
						
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
					
						if(empty($sale_pp)){
						
							$child['sale_price'] ="";
						}else{		
					
							$child['sale_price'] = html_entity_decode(strip_tags(wc_price($product->get_price_excluding_tax())));
					
						}
					
							$tax_price_regular = $product->get_regular_price();
							$tax_rat_regular = $matched_tax_rates['rate'];
								 
							$p =  $product->get_regular_price();
														
							$t =  $p * 100 / ($tax_rat_regular + 100);
							
							$child['regular_price'] = html_entity_decode(strip_tags(wc_price($t)));
					
						}
						
					$product2 = new WC_Product($child_id);
					$product2 = wc_get_product($child_id); 
						
					$grouped_products = $product2->get_children();
					
					
					if($product2->is_type( 'grouped' ) ){
						
					$child['sale_price'] = "";
					
                                        $child['regular_price'] = "";
                                       
                                        $all_prices = array();

					foreach ( $grouped_products as $child_id ) {

						$all_prices[] = get_post_meta( $child_id, '_price', true );
						
						
					}

					 if ( $max ){
				              $child['on_sale'] = "Sale!";
		    	                }else{
				              $child['on_sale'] = "";
			                 }

					$max = max($all_prices);
                                        $min = min($all_prices);

                                        $child['regular_price'] = html_entity_decode(strip_tags(wc_price($min)));
					$child['sale_price'] = html_entity_decode(strip_tags(wc_price($max)));
					
				}

						
						
						}else{
				
							$child['sale_price'] = $sale_pp;
							$child['regular_price'] = html_entity_decode(strip_tags(wc_price($product->get_regular_price())));
					
					}
					}
					
					 //Edit by dev
					 if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() && $product->is_taxable()) {
												
						$_taxobj = new WC_Tax();
						
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
					
							$child['taxes'] = $matched_tax_rates;
						
							//regular_price
							$tax_price_regular = $product->get_regular_price();
							$tax_rat_regular = $matched_tax_rates['rate'];
							$count_tax_regular = $tax_price_regular * $tax_rat_regular / 100;
							$product_with_price_regular = $tax_price_regular + $count_tax_regular;		
						
							$child['tax_product_with_price_regula'] = html_entity_decode(strip_tags(wc_price($product_with_price_regular))); 
					   
						   //sale_price        
						   $tax_price_sale = $product->get_sale_price(); 
					   
					   if($tax_price_sale){
									 
							$tax_rat_sale = $matched_tax_rates['rate'];
							$count_tax_sale = $tax_price_sale * $tax_rat_sale / 100;
							$product_with_price_sale = $tax_price_sale + $count_tax_sale;		
							$child['tax_product_with_price_sale'] = html_entity_decode(strip_tags(wc_price($product_with_price_sale))); 
						
						}else{
							
							$child['tax_product_with_price_sale'] = "";
							
						 }
						
					  }else{
						
						$child['tax_product_with_price_regula'] = "";
						$child['tax_product_with_price_sale'] = "";
						
						
					  }    
		  
					  if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ! wc_prices_include_tax() && $product1->is_taxable()) {
						
							if($product_detail['cart_sale_price']){

								$tax_price_min = $product_detail['cart_sale_price'];							
								$tax_rat_min = $matched_tax_rates['rate'];
								$count_tax_min = $tax_price_min * $tax_rat_min / 100;				
								$child['cart_sale_price'] = $tax_price_min + $count_tax_min;
										
							}
					  }
					  			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($child_id), 'shop_catalog' );
		//Edit end
		
								if(!$thumb)
								$child['image'] = wc_placeholder_img_src();
								else
									$child['image'] = $thumb[0];	 
		//}
		
         //Edit End
         
        $child['product_url'] = "";
		$child['button_text'] = ""	;
		
	 
         if($product1->is_type( 'external' ) ){
				
		        if ( $product1->product_url ){
					$child['product_url'] = $product1->product_url;
					$child['button_text'] = $product1->button_text;
	    		}else{
					$child['product_url'] = "";
				}
			}
			
		if(empty($child['product_url'])){

			unset($child['product_url']);
			unset($child['button_text']);
		}
	 
		

						$product_group[] = $child;
					
		
	}
				}else{
					$product_group['grouped_products'] = "";
				}
				
			
				$child = array('product'=>$product_group);
				
				$product_detail['grouped_products'] = $child;
				
			}
		if($product1->is_type( 'variable' ) ){
		 if ( $product1->is_on_sale() ){
	    	$product_detail['on_sale'] = "Sale!";
	    	}else{
			$product_detail['on_sale'] = "";
		}
		
		$regular_price_min = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'max', true ))));
		$regular_price_max = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'min', true )))); 
		$max_price_reg = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'max', true ))));
		$min_price_reg = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'min', true )))); 
		
		$regular_variation_price = $min_price_reg.'-'.$max_price_reg; 
		$min_price_sale = html_entity_decode(strip_tags(wc_price($product1->get_variation_sale_price( 'min', true )))); 
		$max_price_sale =html_entity_decode(strip_tags(wc_price($product1->get_variation_sale_price( 'max', true ))));
		
		$sale_variation_price = $min_price_sale.'-'.$max_price_sale;
		
		if($min_price_sale == $max_price_sale){
			$product_detail['sale_price'] = '';
			$product_detail['regular_price'] = html_entity_decode($max_price_sale);
		}elseif($regular_price_min == $regular_price_max && $min_price_sale != $max_price_sale){
			$product_detail['regular_price'] = html_entity_decode($regular_price_max);
			$product_detail['sale_price'] = $sale_variation_price;
					
		}elseif($sale_variation_price == $regular_variation_price || $min_price_sale == $max_price_sale){
			$product_detail['sale_price'] = '';
			$product_detail['regular_price'] = html_entity_decode($sale_variation_price);
		
		}else{
			$product_detail['regular_price'] = html_entity_decode($regular_variation_price);
			$product_detail['sale_price'] = html_entity_decode($sale_variation_price);
		}
		
		//Edit by dev		
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() && $product1->is_taxable()) {
				
			$_taxobj = new WC_Tax();
												
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
		
		    //$products[$i]['taxes'] = $matched_tax_rates;
			
						
			//regular price min 
			$tax_price_min = $product1->get_variation_regular_price( 'min', true );				
			
			$tax_rat_min = $matched_tax_rates['rate'];
			$count_tax_min = $tax_price_min * $tax_rat_min / 100;
			
			$product_with_price_min = $tax_price_min + $count_tax_min;		
			$product_detail['tax_product_with_price_regula_min'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 

            //regular price max           
			$tax_price_max = $product1->get_variation_regular_price( 'max', true );
                        		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			$product_detail['tax_product_with_price_regula_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 
			
			if($product_detail['tax_product_with_price_regula_min'] == $product_detail['tax_product_with_price_regula_max']){
				
				$product_detail['regular_tax_price'] = $product_detail['tax_product_with_price_regula_min'];
				
			}else{
			
				$product_detail['regular_tax_price'] = $product_detail['tax_product_with_price_regula_min'] . ' - ' .$product_detail['tax_product_with_price_regula_max'];
			
				
			}
			
		
			if($product_detail['sale_price']){
				
			//sale price min 				
			$tax_price_min = $product1->get_variation_sale_price( 'min', true );							
			$tax_rat_min = $matched_tax_rates['rate'];
			$count_tax_min = $tax_price_min * $tax_rat_min / 100;
			
			$product_with_price_min = $tax_price_min + $count_tax_min;		
			$product_detail['tax_product_with_price_sale_min'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 
           
            //sale price max           
			$tax_price_max = $product1->get_variation_sale_price( 'max', true );                      		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			
			$product_detail['tax_product_with_price_sale_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 			
			$product_detail['sale_tax_price'] = $product_detail['tax_product_with_price_sale_min'] . ' - ' .$product_detail['tax_product_with_price_sale_max'];
			
				
			}else{
				
				$product_detail['tax_product_with_price_sale_min'] = '';
				$product_detail['tax_product_with_price_sale_max'] = '';
				$product_detail['sale_tax_price'] = '';
			  
			 }
				
		  }else{
			  
			  $product_detail['tax_product_with_price_regula_min'] = '';
			  $product_detail['tax_product_with_price_regula_max'] = '';
			  $product_detail['regular_tax_price'] = '';
			  
			  $product_detail['tax_product_with_price_sale_min'] = '';
			  $product_detail['tax_product_with_price_sale_max'] = '';
			  $product_detail['sale_tax_price'] = '';
		  
		  }	
		  //Edit End
		
		}
		$id = $product_detail['id'];
        $plan = $product1;
		$variationsarr = array();
	    $product_variant = array();
	    $product_variations = array();
	       if($product1->is_type( 'variable' ) ){
			$variationsarr = $plan->get_variation_attributes($plan->id);
			 foreach($variationsarr as $variant => $vkey){
				 $a1 = $vkey;
				 array_multisort($a1);
				 
				 $product_variant[] = array(
					'key'   => wc_attribute_label( str_replace( 'attribute_', '', $variant ), $product ),
					'value'   => $a1,
					
				);
			}
			$product_detail['variant'] = $product_variant;
			$variations = $plan->get_available_variations($plan->id);
			
			foreach($variations as $variant1 => $vkey1){
				
				$attrb = $vkey1['attributes'];
				
				$attrb_value = array();
				foreach($attrb as $attr => $attrvalue){
				
					//Edit by dev
					if($attrvalue){					
						$attrb_value[] = $attrvalue;					
					}
					//Edit end
				}
				
				$sale_pp = html_entity_decode(strip_tags(wc_price($vkey1['display_price'])));
				$reg_pp = html_entity_decode(strip_tags(wc_price($sale_pp)));

				$variation_org = wc_get_product($vkey1['variation_id']);
				
				if($variation_org->sale_price){
					$org = $variation_org->sale_price;
				}else{
					
				  if( 'yes' == get_option( 'woocommerce_prices_include_tax' )){
					
					$org = $variation_org->get_price_excluding_tax();
					
					}else{
					
					$org = $variation_org->regular_price;
					
					}
					
				}
				
				if($sale_pp == $reg_pp ){
					$sale_pp = '';
				}
				if($vkey1['display_price'] == $vkey1['display_regular_price'] ){
					$sale_pp = '';
				}
				
				
		//Edit by dev		
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() && $product1->is_taxable()) {
				
			$_taxobj = new WC_Tax();
												
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
		
		    //$products[$i]['taxes'] = $matched_tax_rates;
			
						
			//regular price min 
			$tax_price_min = $vkey1['display_regular_price'];							
			$tax_rat_min = $matched_tax_rates['rate'];
			$count_tax_min = $tax_price_min * $tax_rat_min / 100;
			
			$product_with_price_min = $tax_price_min + $count_tax_min;		
			$regular_tax_price['regular_tax_price'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 
		
			/*
            //regular price max           
			$tax_price_max = $sale_pp;
                        		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			$product_detail['tax_product_with_price_regula_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 
			
			$product_detail['regular_tax_price'] = $product_detail['tax_product_with_price_regula_min'] . ' - ' .$product_detail['tax_product_with_price_regula_max'];
			*/
							
			//sale price min 				
			if($sale_pp){
			
				$tax_price_min = $vkey1['display_price'];							
				$tax_rat_min = $matched_tax_rates['rate'];
				$count_tax_min = $tax_price_min * $tax_rat_min / 100;
				
				$product_with_price_min = $tax_price_min + $count_tax_min;		
				$display_tax_price['display_tax_price'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 
			   
			}else{
								
				$display_tax_price['display_tax_price']  = "";
			
			}
			
            //sale price max           
			/*
			$tax_price_max = $product1->get_variation_sale_price( 'max', true );                      		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			
			$product_detail['tax_product_with_price_sale_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 			
			$product_detail['sale_tax_price'] = $product_detail['tax_product_with_price_sale_min'] . ' - ' .$product_detail['tax_product_with_price_sale_max'];
			*/
			
			
			
			
				
		}else{
			
			$display_tax_price['display_tax_price']  = "";
			$regular_tax_price['regular_tax_price'] = "";
			
		}	
		
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ! wc_prices_include_tax() && $product1->is_taxable()) {
			
			if($vkey1['display_price']){

				$tax_price_min = $vkey1['display_price'];							
				$tax_rat_min = $matched_tax_rates['rate'];
				$count_tax_min = $tax_price_min * $tax_rat_min / 100;				
				$vkey1['display_price'] = $tax_price_min + $count_tax_min;
				
				
			}
		}	
		//Edit End
		  
				
				$product_variations[] =
				array(
				'variation_id' 		 => $vkey1['variation_id'],
				'variation_is_visible' 	 => $vkey1['variation_is_visible'],
				'variation_is_active' 	 => $vkey1['variation_is_active'],
				'is_purchasable'         => $vkey1['is_purchasable'],
				'cart_sale_price'        => $vkey1['display_price'],
				'display_price'          => $sale_pp,
				'display_tax_price' 	 => $display_tax_price['display_tax_price'],
				'display_regular_price'  => html_entity_decode(strip_tags(wc_price($vkey1['display_regular_price']))),
				'regular_tax_price' 	 => $regular_tax_price['regular_tax_price'],
				'origional_price'	 => $org,
				'attributes'             => $attrb_value,
				'image_src'              => $vkey1['image_src'],
				'image_link'             => $vkey1['image_link'],
				'image_title'            => $vkey1['image_title'],
				'image_alt'              => $vkey1['image_alt'],
				'image_caption'          => $vkey1['image_caption'],
				'image_srcset'		 => $vkey1['image_srcset'],
				'image_sizes'		 => $vkey1['image_sizes'],
				'price_html'             => $vkey1['price_html'],
				'availability_html'      => strip_tags($vkey1['availability_html']),
				'sku'                    => $vkey1['sku'],
				'weight'                 => $vkey1['weight'],
				'dimensions'             => $vkey1['dimensions'],
				'min_qty'                => $vkey1['min_qty'],
				'max_qty'                => $vkey1['max_qty'],
				'backorders_allowed'     => $vkey1['backorders_allowed'],
				'is_in_stock'            => $vkey1['is_in_stock'],
				'is_downloadable'        => $vkey1['is_downloadable'],
				'is_virtual'             => $vkey1['is_virtual'],
				'is_sold_individually'   => $vkey1['is_sold_individually'],
				'variation_description'  => $vkey1['variation_description'],
				);
			}
			$product_detail['variations'] = $product_variations;					
			$term_list = wp_get_post_terms($id ,'product_cat',array('fields'=>'ids'));
			$product_detail['parent_category_id'] = $term_list[0];
			
		}
		
		$attachment_ids = $product->get_gallery_attachment_ids();
		$ig = array();
		if(!$attachment_ids){
			$product_detail['gallery_img'] = array();
		}else{
			
			$thumbgal = wp_get_attachment_image_src( get_post_thumbnail_id($theid), 'medium' );
			if($thumbgal){
				$ig[] = $thumbgal[0];
			
				foreach( $attachment_ids as $attachment_id ) 
				{
				
					$myg = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
					$ig[] .= $myg[0];
			
				}
			}
			$product_detail['gallery_img'] = $ig;
			
		}
		
		//$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($theid), 'thumbnail' );
		
		//Etir by dev
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($theid), 'shop_catalog' );
		//Edit end
		
		if(!$thumb)
		$product_detail['image'] = wc_placeholder_img_src();
		else
        	$product_detail['image'] = $thumb[0];
        	
		//endwhile;
		//} else {
			//echo __( 'No Product found' );
		//}
		
		$success = 1;
		
		//Edit by dev
		$my_custom_input_youtube_url = get_post_meta( $theid, 'my_custom_input_youtube_url' );
		$my_custom_input_pdf_file = get_post_meta( $theid, 'my_custom_input_pdf_file' );
		
		if($my_custom_input_youtube_url[0]){
			
			$my_custom_input_youtube_url = $my_custom_input_youtube_url[0];
			
		}else{
			
			$my_custom_input_youtube_url = "";
			
		}
		
		if($my_custom_input_pdf_file[0]){
			
			$my_custom_input_pdf_file = $my_custom_input_pdf_file[0];
			
		}else{
			
			$my_custom_input_pdf_file = "";
		}
		
		$product_detail['youtube_url'] = $my_custom_input_youtube_url;		
		$product_detail['pdf_file'] = $my_custom_input_pdf_file;
		//Edit end
		
		$product_detail=array("product_detail" => $product_detail);
		
		/*
		echo "<pre>";
		print_r($product_detail);
		exit;
		*/
		header('Content-type: application/json');
		echo json_encode(array('success'=> $success, 'data'=>$product_detail));
	}
}
?>
