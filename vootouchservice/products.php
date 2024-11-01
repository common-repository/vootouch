<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WTC_get_products {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_get_products
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
		add_action( 'wtc_get_products', array( $this, 'get_products' ) );
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

	
	public function get_products() {
	
	$postdata = file_get_contents("php://input");
	$postdata = json_decode($postdata);
	$catslug=$postdata->catslug;
	/*
	$theid = 3582;
	$country = 'IN';
	$state = 'GU';
	$postcode = '38009';	
	$city = 'AHMEDABAD';
	*/
	
	$country = $postdata->country;
	$state = $postdata->state;
	$postcode = $postdata->postcode;	
	$city = $postdata->city;
	
			
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

$ordering_args = WC()->query->get_catalog_ordering_args();
if ( isset( $ordering_args['meta_key'] ) ) {
        $args['meta_key'] = $ordering_args['meta_key'];
}
$loop = new WP_Query( array( 'post_type' => array('product'),  's' => $s, 'posts_per_page' => -1, 'orderby' => $ordering_args['orderby'], 'order' => $ordering_args['order'],'meta_key' => $ordering_args['meta_key'],'tax_query'=> $taxquery) );
    
$products=array();
$count= $loop->found_posts;
if ( $loop->have_posts() ) {  
	$i=0;
		while ( $loop->have_posts() ) : $loop->the_post();
		$products[$i]['product_id'] = $theid = get_the_ID();
		$product = new WC_Product($theid); 
		$product1 = wc_get_product($theid); 
		//echo "<pre>"; print_r($product);exit;
		$products[$i]['title']= html_entity_decode(get_the_title());
		$products[$i]['qty']= "";
		
		$products[$i]['currency_symbol']= html_entity_decode(get_woocommerce_currency_symbol());
		
		$products[$i]['description']=  strip_tags($product->post->post_content);
		$products[$i]['status']= $product->post->post_status;
		$products[$i]['rating'] = strip_tags($product->get_average_rating());
		
		$id = $products[$i]['product_id'];
	    $term_list = wp_get_post_terms($id ,'product_cat',array('fields'=>'ids','orderby' => 'parent', 'order' => 'DESC'));
	    $products[$i]['parent_category_id'] = $term_list[0];	
		
		if ( wc_tax_enabled()){
			$products[$i]['suffix'] = strip_tags($product1->get_price_suffix());
		}
		
		if($product1->is_type( 'simple' ) || $product1->is_type( 'external' ) || $product1->is_type( 'grouped' ) ){
			
			if ( $product->is_on_sale() ){
				$products[$i]['on_sale'] = "Sale!";
	    		}else{
				$products[$i]['on_sale'] = "";
			}
			
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				
				$sale_pri = html_entity_decode(strip_tags(wc_price($product->get_price_including_tax())));
				$reg_pri = html_entity_decode(strip_tags(wc_price($product->get_display_price($product1->get_regular_price()))));
				
				if($sale_pri == $reg_pri ){
					$products[$i]['sale_price'] = '';
					$products[$i]['regular_price'] = $reg_pri;
				}else{
					$products[$i]['sale_price'] = $sale_pri;
					$products[$i]['regular_price'] = $reg_pri;
				}
			
			}else{
				$sale_pp = $product->get_sale_price();
				if(empty($sale_pp)){
					$sale_pp = $product->get_sale_price();
				}else{
					$sale_pp = html_entity_decode(strip_tags(wc_price($product->get_sale_price())));
				}
				$products[$i]['sale_price'] = $sale_pp;
				$products[$i]['regular_price'] = html_entity_decode(strip_tags(wc_price($product->get_regular_price()))); 
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
		
		
			//regular_price
			$tax_price_regular = $product1->get_regular_price();
			$tax_rat_regular = $matched_tax_rates['rate'];
			$count_tax_regular = $tax_price_regular * $tax_rat_regular / 100;
			$product_with_price_regular = $tax_price_regular + $count_tax_regular;		
			$products[$i]['tax_product_with_price_regula'] = html_entity_decode(strip_tags(wc_price($product_with_price_regular))); 
           
           //sale_price        
           $tax_price_sale = $product1->get_sale_price(); 
           
           if($tax_price_sale){
			   	         
            $tax_rat_sale = $matched_tax_rates['rate'];
			$count_tax_sale = $tax_price_sale * $tax_rat_sale / 100;
			$product_with_price_sale = $tax_price_sale + $count_tax_sale;		
			$products[$i]['tax_product_with_price_sale'] = html_entity_decode(strip_tags(wc_price($product_with_price_sale))); 
			
			}else{
				
				$products[$i]['tax_product_with_price_sale'] = "";
				
			 }
			
		   }else{
			
			$products[$i]['tax_product_with_price_regula'] = "";
			$products[$i]['tax_product_with_price_sale'] = "";
			
			
		  }    

                               $grouped_products = $product1->get_children();
				
				
				if($product1->is_type( 'grouped' ) ){
				
				foreach ( $grouped_products as $child_id ) {
					
					$all_prices[] = get_post_meta( $child_id, '_price', true );
					
				}

                              

				$max = max($all_prices);
                                $min = min($all_prices);

                                if ( $min ){
				    $products[$i]['on_sale'] = "Sale!";
		    	       }else{
				   $products[$i]['on_sale'] = "";
			      }

                                $products[$i]['origional_price'] = $min;                               
                                $products[$i]['regular_price'] = html_entity_decode(strip_tags(wc_price($max)));
                                $products[$i]['sale_price'] = html_entity_decode(strip_tags(wc_price($min)));
                                $products[$i]['cart_sale_price'] = $min;

                         }
         //Edit End
		
		}
		
		if($product1->is_type( 'variable' ) ){
			if ( $product1->is_on_sale() ){
				$products[$i]['on_sale'] = "Sale!";
			}else{
				$products[$i]['on_sale'] = "";
			}
		
		$regular_price_min = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'max', true ))));		
		$regular_price_max = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'min', true )))); 		
		$max_price_reg = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'max', true ))));			
		$min_price_reg = html_entity_decode(strip_tags(wc_price($product1->get_variation_regular_price( 'min', true )))); 		
		$regular_variation_price = $min_price_reg; 
		$min_price_sale = html_entity_decode(strip_tags(wc_price($product1->get_variation_sale_price( 'min', true )))); 			
		$max_price_sale =html_entity_decode(strip_tags(wc_price($product1->get_variation_sale_price( 'max', true ))));			
		$sale_variation_price = $min_price_sale;
		
		
		if($min_price_sale == $max_price_sale){
			$products[$i]['sale_price'] = '';
			$products[$i]['regular_price'] = html_entity_decode($max_price_sale);
		}elseif($regular_price_min == $regular_price_max && $min_price_sale != $max_price_sale){
			$products[$i]['regular_price'] = html_entity_decode($regular_price_max);
			$products[$i]['sale_price'] = $sale_variation_price;
			
		}elseif($sale_variation_price == $regular_variation_price || $min_price_sale == $max_price_sale){
			$products[$i]['sale_price'] = '';
			$products[$i]['regular_price'] = html_entity_decode($sale_variation_price);
		
		}else{
			$products[$i]['regular_price'] = html_entity_decode($regular_variation_price);
			$products[$i]['sale_price'] = html_entity_decode($sale_variation_price);
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
			$products[$i]['tax_product_with_price_regula_min'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 
           
            //regular price max           
			$tax_price_max = $product1->get_variation_regular_price( 'max', true );
                        		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			$products[$i]['tax_product_with_price_regula_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 
			
			
			if($products[$i]['tax_product_with_price_regula_min'] == $products[$i]['tax_product_with_price_regula_max']){
				
				$products[$i]['regular_tax_price'] = $products[$i]['tax_product_with_price_regula_min'];
			
			}else{
				
				$products[$i]['regular_tax_price'] = $products[$i]['tax_product_with_price_regula_min'] . ' - ' .$products[$i]['tax_product_with_price_regula_max'];
			}
			
				
			if($products[$i]['sale_price']){
				
			//sale price min 				
			$tax_price_min = $product1->get_variation_sale_price( 'min', true );							
			$tax_rat_min = $matched_tax_rates['rate'];
			$count_tax_min = $tax_price_min * $tax_rat_min / 100;
			
			$product_with_price_min = $tax_price_min + $count_tax_min;		
			$products[$i]['tax_product_with_price_sale_min'] = html_entity_decode(strip_tags(wc_price($product_with_price_min))); 
           
            //sale price max           
			$tax_price_max = $product1->get_variation_sale_price( 'max', true );                      		   	         
            $tax_rat_max = $matched_tax_rates['rate'];			
			$count_tax_max = $tax_price_max * $tax_rat_max / 100;			
			$product_with_price_max = $tax_price_max + $count_tax_max;		
			
			$products[$i]['tax_product_with_price_sale_max'] = html_entity_decode(strip_tags(wc_price($product_with_price_max))); 			
			$products[$i]['sale_tax_price'] = $products[$i]['tax_product_with_price_sale_min'] . ' - ' .$products[$i]['tax_product_with_price_sale_max'];
			
				
			}else{
				
				$products[$i]['tax_product_with_price_sale_min'] = '';
				$products[$i]['tax_product_with_price_sale_max'] = '';
				$products[$i]['sale_tax_price'] = '';
			  
			 }
				
		  }else{
			  
			  $products[$i]['tax_product_with_price_regula_min'] = '';
			  $products[$i]['tax_product_with_price_regula_max'] = '';
			  $products[$i]['regular_tax_price'] = '';
			  
			  $products[$i]['tax_product_with_price_sale_min'] = '';
			  $products[$i]['tax_product_with_price_sale_max'] = '';
			  $products[$i]['sale_tax_price'] = '';
		  
		  }	
		  //Edit End    
			
		}

		//$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($theid), 'thumbnail' );		
		
		//Etir by dev
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($theid), 'shop_catalog' );
		//Edit end
			
		if(!$thumb)
		$products[$i]['image'] = wc_placeholder_img_src();
		else
        $products[$i]['image'] = $thumb[0];
			$i++;
		endwhile;
		} else {
			//echo __( 'No products found' );
		}
		
		$products=array("products" => $products);
		
		/*
		echo "<pre>";
		print_r($products);
		exit;
		*/	
		
		header('Content-type: application/json');
		echo json_encode(array('success'=> 1,'count' =>$count,'data'=>$products));
		}
}
?>
