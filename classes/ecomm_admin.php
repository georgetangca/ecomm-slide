<?php
// BEGIN MAIN CLASS
if( !class_exists('e_comm_admin') ){	
	class e_comm_admin extends e_comm_slider{	  
		// Define post type and taxonomy name
		private $options_page;
	  
		function __construct(){
			// Set global plugin defaults
			add_action( 'admin_init', array($this, 'admin_init') );
			add_action( 'admin_menu', array($this, 'admin_menu') );
			add_action( 'admin_print_styles', array($this, 'load_css_for_options_page') );
		  
			
                        // Add menu items to the WordPress admin bar
			add_action( 'wp_before_admin_bar_render', array($this, 'wp_admin_bar') );

		}
               
		function wp_admin_bar() {
			global $wp_admin_bar;
			$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 
                                                    'id' => 'stjoseph_ecomm_slider_settings', 
                                                    'title' => __('stjEcomm Slider Options', 'stjoseph_ecomm'), 
                                                    'href' => admin_url( 'edit.php?post_type='.$this->post_type.'&page=options' )) );
		}
		
		function load_css_for_options_page(){
			if( @$_GET['post_type'] == $this->post_type && @$_GET['page'] == 'options' )
				wp_enqueue_style('options_page_css', plugins_url('/css/admin.css',dirname(__FILE__)));
		}
		
		
		
		function get_include_contents( $filename ) {
			if( is_file($filename) ) {
				ob_start();
				include $filename;
				return ob_get_clean();
			}
			return false;
		}
	  
		
		
		function admin_init(){ 
			// Register plugin options
			register_setting( 'stjoseph_ecomm_slider-settings-group', 'stjoseph_ecomm_slider_options', array($this, 'update_options') ); 
                        
                        add_action( 'do_meta_boxes', array($this,'ecomm_image_box' ));
	
			// Add meta boxes to our post type
			add_meta_box( 'e_comm_slider_meta_box', __('Sidebar Options', 'stjoseph_ecomm'), array($this, 'meta_box_content'), $this->post_type );
			// Save meta data when saving our post type
			add_action( 'save_post', array($this, 'save_meta_data') );
                        
                        add_filter( 'manage_edit-'.$this->post_type.'_columns', array($this, 'add_ecomm_columns') );
                        add_action('manage_'.$this->post_type.'_posts_custom_column', array(&$this, 'show_ecomm_columns'));
                        
		//	add_action( 'manage_posts_custom_column',  array($this, 'show_ecomm_columns') );
			// Add our custom filters to the stjEcomms listing page
			add_action( 'restrict_manage_posts', array($this, 'manage_posts_by_category') );
                        add_action( 'restrict_manage_posts', array($this, 'manage_posts_by_tag') );
		}
	  
		function admin_menu(){
			// Create options page
			$this->options_page = add_submenu_page( 'edit.php?post_type='.$this->post_type, 
                                                                __('eComm Slider Options', 'stjoseph_ecomm'), 
                                                                __('Slider Options', 'stjoseph_ecomm'), 
                                                                'manage_options', 
                                                                'options', 
                                                                array($this, 'options_page') );
		}
	  
		function options_page(){ 
			// Load options page
			include(dirname(dirname(__FILE__)).'/includes/options_page.php' ); 
		}
                
               
                function ecomm_image_box() {

                       // $meteor_image_options = get_option('meteorslides_options');

                       // $meteor_image_title = __( 'Slide Image', 'meteor-slides' ) . ' (' . $meteor_image_options['slide_width'] . 'x' . $meteor_image_options['slide_height'] . ')';

                        remove_meta_box( 'postimagediv', $this->post_type, 'side' );
                        add_meta_box( 'postimagediv', 'Product Image', 'post_thumbnail_meta_box', $this->post_type, 'normal', 'high' );

                }
	  
		function meta_box_content() {
                        //add_meta_box( 'postimagediv', $meteor_image_title, 'post_thumbnail_meta_box', 'slide', 'normal', 'high' );
			include(dirname(dirname(__FILE__)).'/includes/meta_box.php' );
                        
		}
			
		function save_meta_data( $post_id ) {
			// If this is an auto save, our form has not been submitted, so we don't want to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
			// Is the post type correct?
			if ( isset($_POST['post_type']) && $this->post_type != $_POST['post_type'] ) return $post_id;
			// Verify this came from our screen and with proper authorization, because save_post can be triggered at other times
			if (!isset($_POST['ecomm_slider_noncename']) || !wp_verify_nonce( $_POST['ecomm_slider_noncename'], 'ecomm_update_meta' )) { 
                            return $post_id; 
                        }
			// Can user edit this post?
			if ( !current_user_can('edit_post', $post_id) ) return $post_id;
			// Setup array for the data we are saving	
			// keep the adv for each $data = array('_e_comm_slider_target', '_e_comm_slider_url', '_e_comm_slider_top_show_ad_code', '_e_comm_slider_top_ad_code','_e_comm_slider_bottom_show_ad_code', '_e_comm_slider_bottom_ad_code');
			$data = array('_e_comm_slider_target', '_e_comm_slider_url');
			
                        // Use this filter to add postmeta to save for the default post type
			$data = apply_filters('stjoseph_ecomm_slider_add_meta_to_save', $data);
			// Save meta data
			foreach($data as $meta){
				// Get current meta
				$current = get_post_meta($post_id, $meta, TRUE);
				// Get new meta
				$new = $_POST[$meta];
				// If the new meta is empty, delete the current meta
				if( empty($new) ) delete_post_meta($post_id, $meta);
				// Otherwise, update the current meta
				else update_post_meta($post_id, $meta, $new);
			}
		}
	  
		
		function add_ecomm_columns( $columns ){
			// Create a new array so we can put columns in the order we want
			$new_columns = array();
			// Transfer columns to new array and append ours after the desired elements
			foreach($columns as $key => $value){
				$new_columns[$key] = $value;
				if($key == 'title'){
                                        $new_columns['thumbnail'] = __('Image', 'stjoseph_ecomm');
					$new_columns[$this->taxonomy] = __('Categories', 'stjoseph_ecomm');
                                	$new_columns[$this->tag_taxonomy] = __('Tags', 'stjoseph_ecomm');
                                }
			}
			// Return the new column configuration
			return $new_columns;
		}

		function show_ecomm_columns( $name ) {
			global $post;
			// Display our categories on the stjEcomms listing page
			switch ( $name ) {
                                case 'thumbnail':
                                        if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
                                             the_post_thumbnail('thumbnail');
                                        }
                                        break;
                                        
				case $this->taxonomy:
					$terms = get_the_terms( $post->ID, $this->taxonomy );
					if( $terms ){
						$links = array();
						foreach( $terms as $term ){
							$links[] = '<a href="edit.php?post_type='.$this->post_type.'&'.$this->taxonomy.'='.$term->slug.'">'.$term->name.'</a>';
						}
						echo implode(', ', $links);
					}
					else
						_e('No Categories', 'stjoseph_ecomm');
					break;
                                  
                                case $this->tag_taxonomy:
                                    $terms = get_the_terms( $post->ID, $this->tag_taxonomy);
					if( $terms ){
						$links = array();
						foreach( $terms as $term ){
							$links[] = '<a href="edit.php?post_type='.$this->post_type.'&'.$this->tag_taxonomy.'='.$term->slug.'">'.$term->name.'</a>';
						}
						echo implode(', ', $links);
					}
					else
						_e('No Tags', 'stjoseph_ecomm');
					break;
                                 
                                 default: break;
                                    
			}
		}
		
		function manage_posts_by_category(){
			global $typenow;
			// If we are on our custom post type screen, add our custom taxonomy as a filter
			if( $typenow == $this->post_type ){
				$taxonomy = get_terms($this->taxonomy); 
				if( $taxonomy ): //print_r($taxonomy); ?>
					<select name="<?php echo $this->taxonomy; ?>" id="<?php echo $this->taxonomy; ?>" class="postform">
						<option value="">Show All Categories</option><?php
						foreach( $taxonomy as $terms ): ?>
							<option value="<?php echo $terms->slug; ?>"<?php if( isset($_GET[$this->taxonomy]) && $terms->slug == $_GET[$this->taxonomy] ) echo ' selected="selected"'; ?>><?php echo $terms->name; ?></option><?php
						endforeach; ?>
					</select><?php
				endif;
			}
		}
                
                function manage_posts_by_tag(){
			global $typenow;
			// If we are on our custom post type screen, add our custom taxonomy as a filter
			if( $typenow == $this->post_type ){
				$taxonomy = get_terms($this->tag_taxonomy); 
				if( $taxonomy ): //print_r($taxonomy); ?>
					<select name="<?php echo $this->tag_taxonomy; ?>" id="<?php echo $this->tag_taxonomy; ?>" class="postform">
						<option value="">Show All Tags</option><?php
						foreach( $taxonomy as $terms ): ?>
							<option value="<?php echo $terms->slug; ?>"<?php if( isset($_GET[$this->tag_taxonomy]) && $terms->slug == $_GET[$this->tag_taxonomy] ) echo ' selected="selected"'; ?>><?php echo $terms->name; ?></option><?php
						endforeach; ?>
					</select><?php
				endif;
			}
		}
		
		function get_options(){ 
			// Get options from database
			$options = get_option('stjoseph_ecomm_slider_options'); 
			// If nothing, return false
			if( !$options ) return FALSE;
			// Otherwise, return the options as an object (my personal preference)
			return (object) $options;
		}
		
		function update_options( $options = array() ){
			// Get plugin default options as an array
			$defaults = (array) $this->options;
			// Get new options as an array
			$options = (array) $options;
			// Merge the arrays allowing the new options to override defaults
			$options = wp_parse_args( $options, $defaults );
			// Validate options
			foreach( $options as $option => $value ){
				$options[$option] = $this->validate_options( $option, $value );
				if( $value === FALSE ) unset($options[$option]);
			}
			// Return new options array
			return $options;
		}
               
		function save_options( $options ){
			// Takes an array or object and saves the options to the database after validating
			update_option('stjoseph_ecomm_slider_options', $this->update_options($options));
		}
		
		function option_management(){
                        // Get existing options array, if available
			$options = (array) $this->get_options();
			// If unavailable, create an empty array
			if( !$options ) $options = $this->options;
			// Properly saves options and updates plugin version
			$this->save_options( $options );
				
		}
	}  
}
?>