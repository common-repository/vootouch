<?php
class WTC_Settings {

	private static $instance = null;

	/**
	 * Get singleton instance of class
	 *
	 * @return null|WTC_Settings
	 */
	 public $options;
	 
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
	 * Hooks
	 */

	public function add_stylesheet(){
	wp_enqueue_style( 'wtc-admin', plugin_dir_url( WTC_PLUGIN_FILE ) . 'css/style.css');
	}
	
	private function hooks() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'wp_ajax_wtc_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_stylesheet'));
	}
	
	
	/**
	 * Add menu pages
	 */
	public function add_menu_pages() {
		add_menu_page( 'Overview', 'Vootouch', 'manage_options', 'wtc', array( $this, 'screen_main' ));
	}
		 
	/**
	 * Save settings via AJAX
	 *
	 */
		public function save_settings() {
		
		// Security check
		check_ajax_referer( 'wtc-ajax-security', 'ajax_nonce' );
		
		// Permission check
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '0';
			exit;
		}

		// Get options
		$options = VooTouch::get()->get_options();

		// Setup variables
		$fields = explode( ',', $_POST['fields'] );
		$custom = explode( ',', $_POST['custom'] );

		// Update options
		$options['get_posts'][$_POST['post_type']] = array(
			'enabled' => $_POST['enabled'],
			'fields'  => $fields,
			'custom'  => $custom
			
		);
		
		// Save webservice
		VooTouch::get()->save_options( $options );

		exit;
	}
	/************************/


	/**
	 * The main screen
	 */
	public function screen_main() {
		
		if(function_exists( 'wp_enqueue_media' )){
    	wp_enqueue_media();
		}else{
		
		    wp_enqueue_style('thickbox');
		    wp_enqueue_script('media-upload');
		    wp_enqueue_script('thickbox');
		    
		}
		
	?>
	<script>
    jQuery(document).ready(function($) {
		
        $('.screen_logo_upload').click(function(e) {
            e.preventDefault();

            var custom_uploader = wp.media({
                title: 'Custom Image',
                button: {
                    text: 'Upload Image'
                },
                multiple: false  // Set this to true to allow multiple files to be selected
            })
            .on('select', function() {
			$('#loadingmessage').show();
			$('.after').hide();
            var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('.screen_logo').attr('src', attachment.url).load(function() {
			$('#loadingmessage').hide(); $('.after').show(); });
            //$('.screen_logo').attr('src', attachment.url);
                $('.screen_logo_url').val(attachment.url);

            })
            .open();
        });
         
    });
	</script>
		<?php
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'wtc_settings'; 

		
		  if (!empty($_POST['screen_logo'])) {
			$image_url = $_POST['screen_logo'];
			$sql = $wpdb->prepare("UPDATE $table_name SET `image_url`= %s Where `id`= %d" , $image_url,'1');
			$wpdb->query($sql);
		  }
  
		  if (!empty( $_POST['mv_cr_section_color'])) {
			$back_color = $_POST['mv_cr_section_color'];
			$sql1 = $wpdb->prepare("UPDATE $table_name SET `back_color`= %s Where `id`= %d" , $back_color,'1');
			$wpdb->query($sql1);
		  }
		  if (!empty( $_POST['font_color'])) {
			$font_color = $_POST['font_color'];
			$sql2 = $wpdb->prepare("UPDATE $table_name SET `font_color`= %s Where `id`= %d" , $font_color,'1');
			$wpdb->query($sql2);
		  } 

                  if(!empty( $_POST['th_back_color'])) {
			$th_back_color = $_POST['th_back_color'];
			$sql9 = $wpdb->prepare("UPDATE $table_name SET `th_back_color`= %s Where `id`= %d" , $th_back_color,'1');
			$wpdb->query($sql9);
		  }
		  if(!empty( $_POST['icon_color'])) {
			$icon_color = $_POST['icon_color'];
			$sql10 = $wpdb->prepare("UPDATE $table_name SET `icon_color`= %s Where `id`= %d" , $icon_color,'1');
			$wpdb->query($sql10);
		  } 
		  if (!empty( $_POST['page_shape_color'])) {
			$page_shape_color = $_POST['page_shape_color'];
			$sql11 = $wpdb->prepare("UPDATE $table_name SET `page_shape_color`= %s Where `id`= %d" , $page_shape_color,'1');
			$wpdb->query($sql11);
		  }
		  if(!empty( $_POST['name_title_color'])) {
			$name_title_color = $_POST['name_title_color'];
			$sql12 = $wpdb->prepare("UPDATE $table_name SET `name_title_color`= %s Where `id`= %d" , $name_title_color,'1');
			$wpdb->query($sql12);
		  } 
		  if(!empty( $_POST['splash_screen_color'])) {
			$splash_screen_color = $_POST['splash_screen_color'];
			$sql13 = $wpdb->prepare("UPDATE $table_name SET `splash_screen_color`= %s Where `id`= %d" , $splash_screen_color,'1');
			$wpdb->query($sql13);
		  } 

		  if (!empty( $_POST['editor-terms'])) {
			$editor_terms = $_POST['editor-terms'];
			$sql3 = $wpdb->prepare("UPDATE $table_name SET `terms`= %s Where `id`= %d" , $editor_terms,'1');
			$wpdb->query($sql3);
		  }
		  if (!empty( $_POST['editor-contact'])) {
			$editor_contact = $_POST['editor-contact'];
			$sql4 = $wpdb->prepare("UPDATE $table_name SET `contact`= %s Where `id`= %d" , $editor_contact,'1');
			$wpdb->query($sql4);
		  }
				
		if(isset($_POST['submit'])){
		
			 if (isset($_POST['page_view'])) {
				
				$page_view = $_POST['page_view'];
				$sql5 = $wpdb->prepare("UPDATE $table_name SET `page_view`= %s Where `id`= %d" , $page_view,'1');
				$wpdb->query($sql5);
			
			  }
		 
		    if (isset($_POST['product_view'])) {
				
				$product_view = $_POST['product_view'];
				$sql6 = $wpdb->prepare("UPDATE $table_name SET `product_view`= %s Where `id`= %d" , $product_view,'1');
				$wpdb->query($sql6);
			
			  }
			  if (isset($_POST['category_view'])) {
				
				$category_view = $_POST['category_view'];
				$sql7 = $wpdb->prepare("UPDATE $table_name SET `category_view`= %s Where `id`= %d" , $category_view,'1');
				$wpdb->query($sql7);
			
			  }
		  
		}	
						
	$sql_all = "SELECT * FROM $table_name Where id=1";
	$results = $wpdb->get_results($sql_all); 
	
    foreach( $results as $result ) {
		
        $bk_color =  $result->back_color;
	$img_url =  $result->image_url;
        $fnt_color =  $result->font_color;
        $th_back_color =  $result->th_back_color;
        $icon_color =  $result->icon_color;
        $page_shape_color =  $result->page_shape_color;
        $name_title_color =  $result->name_title_color;   
        $splash_screen_color = $result->splash_screen_color;
        $terms = $result->terms;
	$contact = $result->contact;
	$page_view = $result->page_view;
	$product_view = $result->product_view;
	$category_view = $result->category_view;
		
	}
	?>
		<div class="nowrap" id="wws-wrap">
			<?php $default_url =  plugin_dir_url( WTC_PLUGIN_FILE ) . 'images/logo.png'; ?>
			
			<div class="screen">
				<?php
				$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'overview';
				if(isset($_GET['tab'])) $active_tab = $_GET['tab'];
				?>
				<h2 class="nav-tab-wrapper">
				  <a  href="?page=wtc&amp;tab=overview" class="nav-tab <?php echo $active_tab == 'overview' ? 'nav-tab-active' : ''; ?>" id="nav-tab1">Overview</a>
				  <a  href="?page=wtc&amp;tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>" id="nav-tab2">Settings</a>
				  <a  href="?page=wtc&amp;tab=terms" class="nav-tab <?php echo $active_tab == 'terms' ? 'nav-tab-active' : ''; ?>" id="nav-tab3">Terms & conditions</a>
				  <a  href="?page=wtc&amp;tab=contact" class="nav-tab <?php echo $active_tab == 'contact' ? 'nav-tab-active' : ''; ?>" id="nav-tab4">Contact</a>
                                 <a  href="?page=wtc&amp;tab=notification" class="nav-tab <?php echo $active_tab == 'notification' ? 'nav-tab-active' : ''; ?>" id="nav-tab5">Notification</a>
				 <!-- <a  href="?page=wtc&amp;tab=page" class="nav-tab <?php // echo $active_tab == 'page' ? 'nav-tab-active' : ''; ?>" id="nav-tab5">Page Settings</a>-->
				</h2>
				
				
					<?php if($active_tab == 'overview') { ?>
					
					<div id="Overview" class="w3-container overview">
						<p>Vootouch is Woocommerce plugin. It create the bridge between the mobile Application and Website. Whatever changes done from Woocommere Admin Interface ,those changes will be display immediatly on Applicaiton.</p>
						<h4>Vootouch FEATURES</h4>
						<ul class="feature">
							<li>Flattering Design for the Woocommerce</li>
							<li>Change the UI of Application.</li>
							<li>It Covers All by default features of Woocommerce.</li>
							<li>Categries, Sub categires, Product , Product Details</li>
							<li>Checkout and Payment method.</li>
							<li>Order List & Order Details</li>
							<li>Always Allow to upgrade</li>
							<li>Native applications</li>
						</ul>
					</div>
					
					<?php } ?>
					
					<?php  if($active_tab == 'settings') { ?>
				
					<div class ="section">
					<?php
						$active_section = isset($_GET['section']) ? $_GET['section'] : 'general';
						if(isset($_GET['section'])) $active_section = $_GET['section'];
					?>
					<!--<h4 class="nav-tab-wrapper">
					  <a  href="?page=wtc&amp;tab=settings&amp;section=general" class="nav-section <?php echo $active_section == 'general' ? 'nav-tab-active' : ''; ?>" id="nav-tab2">General</a> |
					  <a  href="?page=wtc&amp;tab=settings&amp;section=category" class="nav-section <?php echo $active_section == 'category' ? 'nav-tab-active' : ''; ?>" id="nav-tab6">Category</a> |
					  <a  href="?page=wtc&amp;tab=settings&amp;section=page" class="nav-section <?php echo $active_section == 'page' ? 'nav-tab-active' : ''; ?>" id="nav-tab5">Product</a>  
					</h4>-->
					</div>
				<div id="Settings" class="w3-container overview">
                                
                     <div class="setting-tab">
						<h4 class="nav-tab-wrapper">
						  <a  href="?page=wtc&amp;tab=settings&amp;section=general" class="nav-section <?php echo $active_section == 'general' ? 'nav-tab-active' : ''; ?>" id="nav-tab2">General</a><a  href="?page=wtc&amp;tab=settings&amp;section=category" class="nav-section <?php echo $active_section == 'category' ? 'nav-tab-active' : ''; ?>" id="nav-tab6">Category</a><a  href="?page=wtc&amp;tab=settings&amp;section=page" class="nav-section <?php echo $active_section == 'page' ? 'nav-tab-active' : ''; ?>" id="nav-tab5">Product</a>  
						</h4>
				</div>
				
				<?php if( $active_section != 'category' &&  $active_section != 'page') { ?>
									
				<form action="#" name="splash_screen" method="post">
				<div class="cut_upload_logo">
				<div class="show_txt">
					<div class="upload"><p><b>Upload Logo :</b></p>
						<input class="screen_logo_url" type="text" name="screen_logo" size="25" value="<?php if(get_option('screen_logo')){echo get_option('screen_logo'); }elseif($img_url != ''){ echo $img_url; }else{ echo $default_url; }?>">
						<a href="#" class="screen_logo_upload button-primary">Upload Image</a>
					</div>
				</div>
					<div class="show_upload">
						<div id='loadingmessage' style='display:none'>
							<img class="load" src='<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>/images/loding_image.gif' height="100" width="100" />
						</div>
						<div class="after">
							<img class="screen_logo" src="<?php if(get_option('screen_logo')){echo get_option('screen_logo'); }elseif($img_url != ''){ echo $img_url; }else{ echo $default_url; }?>" height="auto" width="150"/>
						</div>
					</div>
                                        </div>
            
                           <script>
				jQuery(document).ready(function($) {   
					$("#splash_screen").click(function (){
					$("#splash_screen_color").trigger("click");
					});
					$("#mv_cr_section").click(function (){
					$("#mv_cr_section_color").trigger("click");
					});
					$("#font_color1").click(function (){
					$("#font_color").trigger("click");
					});
					$("#th_back").click(function (){
					$("#th_back_color").trigger("click");
					});
					$("#icon").click(function (){
					$("#icon_color").trigger("click");
					});
					$("#page_shape").click(function (){
					$("#page_shape_color").trigger("click");
					});
					$("#name_title").click(function (){
					$("#name_title_color").trigger("click");
					});
				});
			</script>
                
                		<div class="cust_color_wrapper">
				<div class="show_back_color app_color">
					<p><b>Select Splash Screen Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="splash_screen_color" type="color" id="splash_screen_color" value="<?php if(!empty($splash_screen_color)){ echo $splash_screen_color;} else{ echo "#96588a"; } ?>" data-default-color="#96588a">
						<div id="splash_screen" class="cust_color">Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/splash-screen.png">
					</div>
				</div>
                <div class="show_back_color app_color">
					<p><b>Select Status Bar Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="mv_cr_section_color" type="color" id="mv_cr_section_color" value="<?php if(!empty($bk_color)){ echo $bk_color;} else{ echo "#96588a"; } ?>" data-default-color="#96588a">
						<div id="mv_cr_section" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/status-bar-color.png">
					</div>
				</div>
				<div class="show_font_color app_color">
					<p><b>Select Toolbar/Header Text Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="font_color" type="color" id="font_color" value="<?php if(!empty($fnt_color)){ echo $fnt_color;}else{ echo "#ffffff"; } ?>" data-default-color="#ffffff">
						<div id="font_color1" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/toolbar-header-text.png">
					</div>
				</div>
				<div class="show_back_color app_color">
					<p><b>Select Toolbar/Header Background Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="th_back_color" type="color" id="th_back_color" value="<?php if(!empty($th_back_color)){ echo $th_back_color;} else{ echo "#96588a"; } ?>" data-default-color="#96588a">
						<div id="th_back" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/toolbar-header-bac.png">
					</div>
				</div>
				<div class="show_font_color app_color">
					<p><b>Select Toolbar/Header Icon Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="icon_color" type="color" id="icon_color" value="<?php if(!empty($icon_color)){ echo $icon_color;}else{ echo "#ffffff"; } ?>" data-default-color="#ffffff">
						<div id="icon" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/toolbar-header-icon-color.png">
					</div>
				</div>
				<div class="show_back_color app_color">
					<p><b>Select Button/Shape Background Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="page_shape_color" type="color" id="page_shape_color" value="<?php if(!empty($page_shape_color)){ echo $page_shape_color;} else{ echo "#96588a"; } ?>" data-default-color="#96588a">
						<div id="page_shape" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/shape-button-color.png">
					</div>
				</div>
				<div class="show_font_color app_color">
					<p><b>Select Name Title Color</b></p>
					<div class="select_color">
						<div class="cust_input">
						<input name="name_title_color" type="color" id="name_title_color" value="<?php if(!empty($name_title_color)){ echo $name_title_color;}else{ echo "#ffffff"; } ?>" data-default-color="#ffffff">
						<div id="name_title" class="cust_color" >Select Color</div>
						</div>
					</div>
					<div class="app-color-img">
					<img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/name-title-color.png">
					</div>
				</div>
				</div>
				
				<?php echo submit_button( __( 'Save Setting', 'WTC' ) ); ?>
				<?php do_action( 'wtc_general_settings' ); ?>
				</form>
				<?php } ?>

				<?php  if($active_section == 'category') { ?>
				<?php
				if($category_view == 0){
					$category_view_column = "checked";
				}else{
					$category_view_column = "unchecked";
				}
				if($category_view == 1){
					$category_view_grid = "checked";
				}else{
					$category_view_grid = "unchecked";
				}
				if($category_view == 2){
					$category_view_layout = "checked";
				}else{
					$category_view_layout = "unchecked";
				}
				if($category_view == 3){
					$category_view_elegent = "checked";
				}else{
					$category_view_elegent = "unchecked";
				}
				if($category_view == 4){
					$category_view_design = "checked";
				}else{
					$category_view_design = "unchecked";
				}
				if($category_view == 5){
					$category_view_old = "checked";
				}else{
					$category_view_old = "unchecked";
				}
				?>
			
				<style>
				table.category_setting { // border:1px solid;	width:100%; }
				table.category_setting tr{  display:block; }
				table.category_setting td{  width: 20%;  display: inline-block; }
				</style>
				<div id="category" class="w3-container">
						<form action="#" name="terms_descterms_desc" method="post">
							<div class="category_setting">
							<div class="text">
									 <h2>Default Setting Application</h2>
									 <h2>Category Listing View Page Setting</h2>
								 </div>
								 <?php $class='class="active"'; ?>
								 <ul class="list">
									 <li <?php if($category_view == 0){ echo $class; } ?>>
								     <span>Category Column: <input type="radio" name="category_view" value="0" <?php echo $category_view_column; ?>></span>
								      <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/category-column.png"></div> 
								     </li>
								  							
									 <li <?php if($category_view == 1){ echo $class; } ?>>
										 <span>Category elegent layout: <input type="radio" name="category_view" value="1" <?php echo $category_view_grid; ?>></span>
										 <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/catagory-elegent-layout.png"></div> 
									 </li>
								 
									 <li <?php if($category_view == 2){ echo $class; } ?>>
										 <span>Category layout: <input type="radio" name="category_view" value="2" <?php echo $category_view_layout; ?>></span>
										 <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/category-column-layout-change-design.png"></div> 
									 </li>
									 
									 <li <?php if($category_view == 3){ echo $class; } ?>>
										 <span>Category row: <input type="radio" name="category_view" value="3" <?php echo $category_view_elegent; ?>></span>
										 <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/category-column-layout.png"></div> 
									 </li>
								
									 <li <?php if($category_view == 4){ echo $class; } ?>>
										 <span>Category elegent design:	<input type="radio" name="category_view" value="4" <?php echo $category_view_design; ?>></span>
										 <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/category-elegent-layout-design-change.png"></div> 
									 </li>
									 <li <?php if($category_view == 5){ echo $class; } ?>>
										 <span>Category design: <input type="radio" name="category_view" value="5" <?php echo $category_view_old; ?>></span>
										 <div class="cat_viewimg"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/category-new-layout.png"></div> 
									 </li>
								 
								</ul>			
							<!--	 <p class="submit_btn"> -->
									<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?>
								<!-- </p>	 -->
						</div>
					</form>
				</div>
				<?php } ?>
				<?php  if($active_section == 'page') { ?>
				
				<?php
				if($page_view == 0){
					$checked_category = "checked";
				}else{					
					$checked_category = "unchecked";
				}				
				if($page_view == 1){					
					$checked_shop = "checked";					
				}else{					
					$checked_shop = "unchecked";
				}				
				if($product_view == 0){					
					$product_view_column = "checked";					
				}else{					
					$product_view_column = "unchecked";
				}				
				if($product_view == 1){					
					$product_view_row = "checked";					
				}else{					
					$product_view_row = "unchecked";
				}				
				if($product_view == 2){					
					$product_view_grid = "checked";					
				}else{					
					$product_view_grid = "unchecked";
				}
				if($product_view == 3){					
					$product_view_list = "checked";					
				}else{					
					$product_view_list = "unchecked";
				}					
				?>
				<style>
				table.page_setting {// border:1px solid;	width:100%; }
				table.page_setting tr{  display:block; }
				table.page_setting td{  width: 20%;  display: inline-block;	}
				</style>
				
					<div id="page" class="w3-container">
						<form action="#" name="terms_descterms_desc" method="post">
							<div class="product_setting">
								<div class="text">
									 <h1>Default Setting Application</h1>
								 <span>Category View:
									<input type="radio" name="page_view" value="0" <?php echo $checked_category; ?>>
								</span>
								
								<span>
									Shop View:
									<input type="radio" name="page_view" value="1" <?php echo $checked_shop; ?>>
								</span>
								<h2 colspan="2" style="font-weight:bold;">Product Listing View Page Setting</h2>
								</div>
								 			
								 
									 
								  <?php $class='class="active"'; ?>
								 <ul class="list">
								 <li <?php if($product_view == 0){ echo $class; } ?>>
								      <span>Product Column:
								      <input type="radio" name="product_view" value="0" <?php echo $product_view_column; ?>>
								      </span>
								      <div class="product_img"><img  src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/product listing latest-layout.png"></div> 
								 </li>
								  
								  <li <?php if($product_view == 1){ echo $class; } ?>>
									 <span>Product Row:
									 <input type="radio" name="product_view" value="1" <?php echo $product_view_row; ?>>
									 </span>
									 <div class="product_img"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/product-listing-change-design1.png"></div> 
								 </li>
								  							
								 <li <?php if($product_view == 2){ echo $class; } ?>>
									 <span>Product List Grid:
									 <input type="radio" name="product_view" value="2" <?php echo $product_view_grid; ?>>
									 </span>
									 <div class="product_img"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/product-listing.png"></div> 
								 </li>
								 <li <?php if($product_view == 3){ echo $class; } ?>>
									 <span>Product List: 
									 <input type="radio" name="product_view" value="3" <?php echo $product_view_list; ?>>
									 </span>
									 <div class="product_img"><img src="<?php echo plugin_dir_url( WTC_PLUGIN_FILE ); ?>images/view_images/product-old-listing.png"></div> 
								 </li>
						
								
								 </ul>
							<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?>

						</div>
					</form>
				</div>					
				<?php } ?>
				<?php } ?>
				
				<?php  if($active_tab == 'terms') { ?>
				<div id="Terms" class="w3-container overview">
					<form action="#" name="terms_descterms_desc" method="post">
					<?php
					$edit_id = 'editor-terms';
					$setting = array( 'media_buttons' => false,'editor_height' => '200px','textarea_rows' => 20 );
					$contentone = $terms;
					wp_editor( $contentone, $edit_id, $setting );
					?>
					<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?> 
					</form>
					
				</div>
				<?php } ?>
				<?php  if($active_tab == 'contact') { ?>
				<div id="Contact" class="w3-container overview">
				<form action="#" name="contact_info" method="post">
				<?php
					$editor_id = 'editor-contact';
					$settings = array( 'media_buttons' => false,'editor_height' => '200px' );
					$content = $contact;
					wp_editor( $content, $editor_id, $settings );
				?>
				<?php echo submit_button( __( 'Submit', 'WTC' ) ); ?> 
				</form>
				</div>
				<?php } ?>
								
			
			<?php  if($active_tab == 'notification') { ?>
					<div id="Contact" class="w3-container overview">
					   
					   <html>
					<head>
						<title>AndroidHive | Firebase Cloud Messaging</title>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, initial-scale=1.0">
						<link rel="shortcut icon" href="//www.gstatic.com/mobilesdk/160503_mobilesdk/logo/favicon.ico">
						<!-- <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css"> -->
                                                <link rel="stylesheet" href="<?php echo plugins_url( 'css/pure-min.css', __DIR__ );  ?>">
						<style type="text/css">
							body{
							}
							div.container{
								width: 1000px;
								margin: 0 auto;
								position: relative;
							}
							legend{
								font-size: 30px;
								color: #555;
							}
							.btn_send{
								background: #00bcd4;
							}
							label{
								margin:10px 0px !important;
							}
							textarea{
								resize: none !important;
							}
							.fl_window{
								width: 400px;
								position: absolute;
								right: 0;
								top:100px;
							}
							pre, code {
								padding:10px 0px;
								box-sizing:border-box;
								-moz-box-sizing:border-box;
								webkit-box-sizing:border-box;
								display:block; 
								white-space: pre-wrap;  
								white-space: -moz-pre-wrap; 
								white-space: -pre-wrap; 
								white-space: -o-pre-wrap; 
								word-wrap: break-word; 
								width:100%; overflow-x:auto;
							}

						</style>
					</head>
					<body>
						<?php
						// Enabling error reporting
						//error_reporting(-1);
						
						//ini_set('display_errors', 'On');
								
						require_once __DIR__ . '/notification/firebase.php';
						require_once __DIR__ . '/notification/push.php';
						require_once __DIR__ . '/notification/iphonebase.php';
												
						$firebase = new Firebase();
						$iphonebase = new Iphonebase();
											
						$push = new Push();
						
						// optional payload
						/*$payload = array();
						$payload['team'] = 'India';
						$payload['score'] = '5.6';*/

						// notification title
						$title = isset($_GET['title']) ? $_GET['title'] : '';
						
						// notification message
						$message = isset($_GET['message']) ? $_GET['message'] : '';
						
						// push type - single user / topic
						$push_type = isset($_GET['push_type']) ? $_GET['push_type'] : '';

						// device type - single user / topic
						$device_type = isset($_GET['device_type']) ? $_GET['device_type'] : '';
						
						// whether to include to image or not
						$include_image = isset($_GET['include_image']) ? TRUE : FALSE;
						
						// notification page
						$page = isset($_GET['page']) ? $_GET['page'] : '';
						
						// notification page
						$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
						
						$push->setPage($page);						
						$push->setTab($tab);
						
						$push->setTitle($title);
						$push->setMessage($message);
																		
						if ($include_image) {
								$push->setImage('http://api.androidhive.info/images/firebase_logo.png');
						} else {
								$push->setImage('');
						}
						
						$push->setIsBackground(FALSE);
						//$push->setPayload($payload);

						$json = '';
						$response = '';

						$admin_url = admin_url(); 
						
						if ($push_type == 'topic') {
								$json = $push->getPush();
                   
                        if ($device_type == '1') {
							global $wpdb;
							$userdata = $wpdb->get_results( "SELECT token FROM wp_wtc_notification where type = 1",ARRAY_A );
							
						for($i=0;$i<count($userdata);$i++){
							if($userdata[$i]['token']!="")
							$userids[] = $userdata[$i]['token'];
						}
                       // echo"<pre>";print_r($userids);
							//$response = $iphonebase->sendAndroidTopic($userids, $json);
							$response = $firebase->sendToTopic('global', $json);
						}
						if ($device_type == '2') {
							global $wpdb;
							$userdata = $wpdb->get_results( "SELECT token FROM wp_wtc_notification where type = 2",ARRAY_A );
							
						for($i=0;$i<count($userdata);$i++){
							if($userdata[$i]['token']!="")
							$device_token[] = $userdata[$i]['token'];
						}
                        //echo"<pre>";print_r($device_token);
							//$to ="72b423addee2c2a7f93f0c6e6f1e616b981d7010096d9efb13304731f8ca2ff3";
							$response = $firebase->sendToIosTopic('global', $message);
							//$response = $iphonebase->sendIphoneTopic($device_token, $message);
						  }
							if ($response != '') {
								
								$responsedata = json_decode($response);								 
								$message_id = $responsedata->message_id;
								
								if($message_id){
									
									$url = $admin_url.'admin.php?page=wtc&tab=notification&success=1';
									wp_redirect( $url );
									exit;
								}
							}
						
						} 
						/*else if ($push_type == 'individual') {
							
							$json = $push->getPush();
							$regId = isset($_GET['regId']) ? $_GET['regId'] : '';
							$response = $firebase->send($regId, $json);
							
							if ($response != '') {
								
								$responsedata = json_decode($response);								 
								$success = $responsedata->success;
								
								if($success == 1){
									
									$url = $admin_url.'admin.php?page=wtc&tab=notification&success=1';
									wp_redirect( $url );
									exit;
								}
							}
        
						}*/
						?>
						<div class="container">
							<div class="fl_window">
								<!--
								<div><img src="http://api.androidhive.info/images/firebase_logo.png" width="200" alt="Firebase"/></div>
								<br/>
								-->
								<?php if ($json != '') { ?>
									<label style="display:none;"><b>Request:</b></label>
									<div class="json_preview" style="display:none;">
										<pre><?php echo json_encode($json) ?></pre>
									</div>
								<?php } ?>
								<br/>
								<?php if ($response != '') { ?>
									<label style="display:none;"><b>Response:</b></label>
									<div class="json_preview" style="display:none;">
										<pre><?php echo json_encode($response) ?></pre>
									</div>
								<?php } ?>

							</div>
							 							    
								<?php
								
								if(isset($_GET['success']) == 1){ 
								
								?>
									<div><h3>Your Notification Is Succesfully Send</h3></div>
							 
							   <?php } ?>
							 
						<!-- 	<form class="pure-form pure-form-stacked" method="get">
								<fieldset>
									<legend>Send to Single Device</legend>

									<label for="redId">Firebase Reg Id</label>
									<input type="text" id="redId" name="regId" class="pure-input-1-2" placeholder="Enter firebase registration id">

									<label for="title">Title</label>
									<input type="text" id="title" name="title" class="pure-input-1-2" placeholder="Enter title">

									<label for="message">Message</label>
									<textarea class="pure-input-1-2" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
									
									<label for="include_image" class="pure-checkbox">
										<input name="include_image" id="include_image" type="checkbox"> Include image
									</label>
																	
									<input type="hidden" name="page" value="wtc"/>
									<input type="hidden" name="tab" value="notification"/>
									<input type="hidden" name="push_type" value="individual"/>
									
									<button type="submit" class="pure-button pure-button-primary btn_send">Send</button>
								</fieldset>
							</form>	
							<br/><br/><br/><br/>-->

							<form class="pure-form pure-form-stacked" method="get">
								<fieldset>
									<legend>Send to Topic `global`</legend>

									<label for="title1">Title</label>
									<input type="text" id="title1" name="title" class="pure-input-1-2" placeholder="Enter title">

									<label for="message1">Message</label>
									<textarea class="pure-input-1-2" name="message" id="message1" rows="5" placeholder="Notification message!"></textarea>
                                                                        <label for='device_type'>Select Device:</label>
                                                                        <select name="device_type">
                                                                          <option value="">select device</option>
                                                                          <option value="1">Android</option>
                                                                          <option value="2">Iphone</option>
                                                                        </select>
									<!--					
									<label for="include_image1" class="pure-checkbox">
										<input id="include_image1" name="include_image" type="checkbox"> Include image
									</label>
									-->
									<input type="hidden" name="page" value="wtc"/>
									<input type="hidden" name="tab" value="notification"/>
									<input type="hidden" name="push_type" value="topic"/>
									<button type="submit" class="pure-button pure-button-primary btn_send">Send to Topic</button>
								</fieldset>
							</form>
						</div>
						</body>
					</html>


					</div>
				<?php } ?>
			
			</div>
		</div>
	<?php
	VooTouch::get();
	}
}
