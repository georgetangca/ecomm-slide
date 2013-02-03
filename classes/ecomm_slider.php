<?php
if(!defined("TWITTER_ACCT")){
    define("TWITTER_ACCT","canadianfamily");    
}

// BEGIN MAIN CLASS
if( !class_exists('e_comm_slider') ){	
	class e_comm_slider {	  
		// Define post type and taxonomy name
		protected $post_type = 'stjecomm',
                          $option_meta = 'stjoseph_ecomm_options',
                          $taxonomy =  'stjoseph_ecomm_category',
                          $tag_taxonomy = 'stjoseph_ecomm_tag', $options;
                         
	  
		function __construct(){
			// Set global plugin defaults
			$this->options = array(	'version' => STJOSEPH_ECOMM_SLIDER_VER,
                                            'numberposts'=>DEFAULT_LOADING_POSTS_NUBMER,
                                            'slide_show_sidebar'=>'1'
                                            );
		  
			// Create the post type that we will pull from
			new ecomm_custom_post_type($this->post_type, 
                                                  array('singular' => __('stjEcomm', 'stjoseph_ecomm'), 
                                                 'plural' => __('stjEcomms', 'stjoseph_ecomm'),
                                                 'slug' => 'stjecomm', 
                                                 'args' => array('supports' => array('title', 'editor', 'author', 'excerpt', 'thumbnail'))));

			// Register the taxonomy for our post type
			new ecomm_custom_taxonomy($this->taxonomy, 
                                                      $this->post_type, 
                                                      array('singular' => __('Category', 'stjoseph_ecomm'), 
                                                            'plural' => __('Categories', 'stjoseph_ecomm'), 
                                                            'slug' => 'stjecomm-cat', //category_slug
                                                            'args' => array('hierarchical' => true)) );
                        
                        // Register the taxonomy for our post type
			new ecomm_custom_taxonomy($this->tag_taxonomy, 
                                                      $this->post_type, 
                                                      array('singular' => __('Tag', 'stjoseph_ecomm'), 
                                                            'plural' => __('Tags', 'stjoseph_ecomm'), 
                                                            'slug' => 'stjecomm-tag',
                                                            'args' => array('hierarchical' => false)) );
                        
                        

			// Perform some maintenance activities on activation
			register_activation_hook( __FILE__, array($this, 'activate') );
			
			// Check if plugin has been updated, if so run activation function
			
                        if( $this->get_options()->version != STJOSEPH_ECOMM_SLIDER_VER ) {
				$this->activate();
                        }
                        
			// Initiate key components
			add_action( 'after_setup_theme', array(&$this, 'after_setup_theme') );
			add_action( 'init', array($this, 'init') );
			
			// Load this plugin last to ensure other plugins don't overwrite theme support
			add_action( 'activated_plugin', array(&$this, 'load_last') );
			
			
			add_filter( 'stjoseph_ecomm_slider_image_size', array(&$this, 'ecomm_slider_image_size_control'), 10, 1 );
                        add_filter( 'stjoseph_ecomm_slider_thumb_image_size', array(&$this, 'ecomm_slider_thumb_image_size_control'), 10, 1 );
                        
                        add_filter('the_content', array(&$this, 'the_content_filter'));
                      
		}
                
                
                function the_content_filter($content){
                      global $post, $wp;
                    
                      if (!is_single() or $post->post_type != $this->post_type) {
                          return $content;                          
                      }

                      remove_filter('the_content', array(&$this, 'the_content_filter'));
                      
                      if ( is_single()) {
                          $template = $this->get_template($post->post_type.'-single');
                      } 

                      ob_start();
                      require ($template);
                      $content = ob_get_contents();
                      ob_end_clean();

                      add_filter('the_content', array(&$this, 'the_content_filter'));

                      return $content;
                }
                
                
                function get_template($template = NULL, $ext = '.php', $type = 'path') {
                      if ( $template == NULL ) {
                           return false;
                      }

                      $themeFile = get_stylesheet_directory() . '/' . $template . $ext;
                      $folder = '/';

                      if ( !file_exists($themeFile) ) {
                           $themeFile = ECOMM_TEMPLATE_DIR. '/' . $template . $ext;
                      }

                      if ( file_exists($themeFile)) {
                           $file = $themeFile;
                      } 
                      return $file;
                 }
               
	  
		function activate() {
			// Make sure user is using WordPress 3.0+
			$this->requires_wordpress_version();
			// Ensure all options are up-to-date when upgrading
			$this->option_management();
			// One time flush of rewrite rules
			//flush_rewrite_rules();
		}
		
		function requires_wordpress_version( $ver = 3 ){
			global $wp_version;
			if( $wp_version < $ver )
				die( printf(__('Sorry, this plugin requires WordPress version %d or later. You are currently running version %s.', 'stjoseph_ecomm'), $ver, $wp_version) );
		}
	  
		function after_setup_theme(){ 
			// Adds support for featured images, which is where the slider gets its images
			add_theme_support( 'post-thumbnails' ); 
                        add_image_size('custom_comm_image_size', ECOMM_MAX_PIC_WIDTH, ECOMM_MAX_PIC_HEIGHT, true ); //george need change 
                        add_image_size('comm_slider_thumb_size', ECOMM_MAX_THUMBNAIL_WIDTH, ECOMM_DEFAULT_THUMBNAIL_HEIGHT, true ); //george need change                        
		}
	  
		function init(){
			// Add support for translations
			load_plugin_textdomain( 'stjoseph_ecomm', FALSE, dirname(plugin_basename(__FILE__)).'/lang/' );
			// Load our js and css files
			add_action( 'wp_print_styles', array($this, 'enqueue_styles') );
			//change enquene as register add_action( 'wp_print_scripts', array($this, 'enqueue_scripts') );
                        
			// Create [stjoseph_ecomm_slider] shortcode
			add_shortcode( 'stjoseph_ecomm_slider', array($this, 'short_code_show_slider') );
			// Enable use of the shortcode in text widgets
			add_filter( 'widget_text', 'do_shortcode' );
			// Add our custom column to the stjEcomms listing page
	                
                        /* Setup AJAX */
                        //george add ajax process 
                        add_action('wp_ajax_get_requested_ecomm_posts_for_front_end', array(&$this, 'get_requested_ecomm_posts_for_front_end'));
                        add_action('wp_ajax_nopriv_get_requested_ecomm_posts_for_front_end', array(&$this, 'get_requested_ecomm_posts_for_front_end'));
                   
                        add_action('wp_ajax_get_single_ecomm_post_for_front_end', array(&$this, 'get_single_ecomm_post_for_front_end'));
                        add_action('wp_ajax_nopriv_get_single_ecomm_post_for_front_end', array(&$this, 'get_single_ecomm_post_for_front_end'));
                   
                        
                        add_action('wp_ajax_share_email_send', array(&$this, 'share_email_send'));
                        add_action('wp_ajax_nopriv_share_email_send', array(&$this, 'share_email_send'));
                     
                        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
          	}
                
               function enqueue_styles(){
			// Loads our styles, only on the front end of the site
			if( !is_admin() ){
                            wp_enqueue_style( 'stjoseph_ecomm_slider_main', plugins_url('/css/slide.css', dirname(__FILE__)) );
			}
		}
		
                
		function enqueue_scripts(){
			// Loads our scripts, only on the front end of the site
			if( !is_admin() ){
				// Get plugin options
				$options = $this->get_options();
				// Load javascript
				$load_js_in_footer = ( $options->load_js_in == 'footer' ) ? TRUE : FALSE;
                                
                               
                                /*
                                 *  put to the theme function.php for process jQuery conflict */
                                wp_enqueue_script( 'stjoseph_ecomm_slider_tools', plugins_url('/js/jquery.tools.min.js', dirname(__FILE__)), array('jquery'), FALSE, $load_js_in_footer );
				wp_enqueue_script( 'stjoseph_ecomm_slider_addr', plugins_url('/js/jquery.address-1.4.min.js', dirname(__FILE__)), array('jquery'), FALSE, $load_js_in_footer );
                                wp_enqueue_script( 'stjoseph_ecomm_slider_main', plugins_url('/js/e_comm_slider.js', dirname(__FILE__)), array('jquery'), FALSE, $load_js_in_footer );
                               
                                
                                
                                // Localize plugin options
				$data = array('version' => STJOSEPH_ECOMM_SLIDER_VER);
				wp_localize_script('stjoseph_ecomm_slider_main', 'e_comm_slider_options', $data);
                                
                                //george add ajax process 
                                wp_localize_script('stjoseph_ecomm_slider_main', 'ECOMM_AJAX', array(
                                       'ajaxurl' => admin_url('admin-ajax.php')
                                       )
                                 );
                                
                                
			}
		}
	  
		function load_last(){
			// Get array of active plugins
			if( !$active_plugins = get_option('active_plugins') ) return;
			// Set this plugin as variable
			$my_plugin = 'stjoseph_ecomm/'.basename(__FILE__);
			// See if my plugin is in the array
			$key = array_search( $my_plugin, $active_plugins );
			// If my plugin was found
			if( $key !== FALSE ){
				// Remove it from the array
				unset( $active_plugins[$key] );
				// Reset keys in the array
				$active_plugins = array_values( $active_plugins );
				// Add my plugin to the end
				array_push( $active_plugins, $my_plugin );
				// Resave the array of active plugins
				update_option( 'active_plugins', $active_plugins );
			}
		}
	  
	  
    		function get_slider_image( $slider_id = NULL, $my_post= null){
		        if($my_post == null){
                            global $post;
                            $my_post = $post;                            
                        }
                        
			$options = $this->get_options();
			// If functionality or image doesn't exist, go ahead and terminate
			if( !function_exists('has_post_thumbnail') || !has_post_thumbnail($my_post->ID) ) return FALSE;
			// Filters allow for use of particular image sizes in the slider
		        /*
                        $image_size = apply_filters('stjoseph_ecomm_slider_image_size', $options->default_img_size);
			// Filters allow for use of particular image sizes in specific sliders
			$image_size = apply_filters('stjoseph_ecomm_slider_image_size_by_id', array('id' => $slider_id, 'image_size' => $image_size) );
			$image_size = $image_size['image_size'];
			// Return the appropriate sized image
			//return get_the_post_thumbnail($my_post->ID, $image_size);
                        */
                        
                         $url = wp_get_attachment_url(get_post_thumbnail_id($my_post->ID));
                         $image =  "<img src='".$url."'  width='".ECOMM_DEFAULT_PIC_WIDTH."' height='".ECOMM_DEFAULT_PIC_HEIGHT."'  />";
                         return $image;
                         //return get_the_post_thumbnail($my_post->ID, 'custom_comm_image_size');
		}
                
                
		
		function get_slider_thumb( $title,  $my_post= NULL ){
			if($my_post == null){
                            global $post;
                            $my_post = $post;                            
                        }
			// If functionality or image doesn't exist, go ahead and terminate
			if( !function_exists('has_post_thumbnail') || !has_post_thumbnail($my_post->ID) ) return FALSE;
		     /*
                        // Filter allows for use of particular thumbnail size in the slider
			$thumb_size = apply_filters('stjoseph_ecomm_slider_thumb_image_size', 'thumbnail');
			// Return the appropriate sized image with corrected title attribute
			return preg_replace('/title="[^"]*"/', 'title="'.$title.'"', get_the_post_thumbnail($my_post->ID, $thumb_size) );
                     */   
                      //  $url = wp_get_attachment_url(get_post_thumbnail_id($my_post->ID));
                        $url = wp_get_attachment_thumb_url(get_post_thumbnail_id($my_post->ID));
                        $image =  "<img src='".$url."'  width='".ECOMM_DEFAULT_THUMBNAIL_WIDTH."' height='".ECOMM_DEFAULT_THUMBNAIL_HEIGHT."'  />";
                        return $image;                        
		}
                
               function get_slider_sidebar_contents($title,  $my_post= NULL){
                      if($my_post == null){
                            global $post;
                            $my_post = $post;                            
                      }
                      
                    // $image_url = wp_get_attachment_url(get_post_thumbnail_id($my_post->ID));
                     $image_url      = wp_get_attachment_thumb_url(get_post_thumbnail_id($my_post->ID));
                     $real_image_url = wp_get_attachment_url(get_post_thumbnail_id($my_post->ID));
                     
                     
                     
                     $bar_title    = $my_post->post_title;
                     $bar_slug     = $my_post->post_name;
                     
                     $bar_content  = $my_post->post_content;
                     $bar_excerpt  = $my_post->post_excerpt;
                     
                     $bar_link_arr = $this->fetch_link_settings($my_post);
                     $return_arr = compact('bar_title','bar_slug','image_url','real_image_url', 'bar_content','bar_excerpt','bar_link_arr');
                     return $return_arr;
               }
		
		function fetch_link_settings($my_post = NULL){
			if($my_post == null){
                            global $post;
                            $my_post = $post;                            
                        }
			// If the destination url is set by the user, use that.  Otherwise, use the permalink
			$destination_url = get_post_meta($my_post->ID, '_e_comm_slider_url', TRUE);
			if( !$destination_url ) $destination_url = get_permalink($my_post->ID);
			// If the target attribute is set the user, use that.  Otherwise, set it to _self
			/*$target = get_post_meta($my_post->ID, '_e_comm_slider_target', TRUE);
			if( !$target ) $target = '_self';
                         */
                        $target = '_blank';
			return compact('destination_url', 'target');
		}
		
		
                
               
		function get_options(){ 
			// Get options from database
			$options = get_option('stjoseph_ecomm_slider_options'); 
			// If nothing, return false
			if( !$options ) $options = $this->options;
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
                
                //valide multiple category, tags
                function valide_terms($option_name, $option_value){
                   if(empty($option_value)){
                      return false; 
                   } else {
                        $term_arr  =  explode(',', $option_value);
                        $term_value = '';
                        $i= 0;
                        $taxonomy = null;
                        if($option_name =='category'){
                           $taxonomy = $this->taxonomy; 
                        } elseif($option_name =='tag'){
                           $taxonomy = $this->tag_taxonomy; 
                        } else {
                            return false;
                        }
                        
                        foreach($term_arr as $item){
                            if(term_exists($item, $taxonomy)){
                                if($i==0){
                                   $term_value .= $item;
                                } else {
                                   $term_value .= ','.$item;
                                }
                                $i++;
                            }
                        }
                        if(empty($term_value)){
                            return false;
                        } else {
                            return $term_value;
                        }
                   } //not empty,process end
                }
		
		function validate_options( $option_name, $option_value ){
			switch( $option_name ){
				case 'version':
					return STJOSEPH_ECOMM_SLIDER_VER;
				
				case 'leadboard_show':
                                case 'sidebar_top_show':
                                case 'sidebar_bottom_show':    
					if( in_array($option_value, array('true', '')) )
						return $option_value;
					break;
                                        
				case 'leaderboard_code':
                                case 'sidebar_top_code':
                                case 'sidebar_bottom_code':
                                   	return $option_value;
					break;
				
                                case 'load_js_in':
					if( in_array($option_value, array('head', 'footer')) )
						return $option_value;
					break;
				case 'post_type':
					if( $option_value != $this->post_type && post_type_exists($option_value) ) 
						return $option_value;
					return $this->post_type;
					break;
				case 'category':
                                case 'tag':
				      return $this->valide_terms($option_name, $option_value); 	
                                      break;
                                        
				case 'id':
					return $option_value;
					break;
				case 'width':
					return $option_value;
					break;
				case 'height':
					return $option_value;
					break;
				case 'numberposts':
					$option_value = (int) $option_value;
					if( is_int($option_value)){ 
                                             if(empty($option_value) ) {
						$option_value = DEFAULT_LOADING_POSTS_NUBMER;
                                             }
                                             return $option_value;
                                        }
					return -1;
					break;
				case 'default_img_size':
					if( in_array($option_value, array('thumbnail', 'medium', 'large', 'full')) )
						return $option_value;
					break;
				default:
					return FALSE;
			}
			return $this->options[$option_name];
		}
		
		function save_options( $options ){
			// Takes an array or object and saves the options to the database after validating
			update_option('stjoseph_ecomm_slider_options', $this->update_options($options));
		}
		
		function option_management(){
                        // Get existing options array, if available
			$options = (array) $this->get_options();
			$this->save_options( $options );
		}
                
                
                
                //George add here 
               function ecomm_slider_image_size_control($image_size){
                     if($image_size=='full'){
                         return array(ECOMM_MAX_PIC_WIDTH,ECOMM_MAX_PIC_HEIGHT);
                     }
                    
                }
                
               function ecomm_slider_thumb_image_size_control($image_size){
                    if($image_size=='thumbnail'){
                         return array(ECOMM_DEFAULT_THUMBNAIL_WIDTH,ECOMM_DEFAULT_THUMBNAIL_HEIGHT);
                     }                   
               }
                
               
               function get_ecomm_all_terms($taxonomy_indicator = 'category', $search=NULL){
                   $args = array(
                      'search'=>$search   
                      ,'parent'=>'0'
                      ,'hide_empty' =>'0'           
                   );
                  if($taxonomy_indicator == 'category') {
                     $taxonomy = $this->taxonomy;
                     $limit_value = $this->options['category'];
                  } elseif($taxonomy_indicator == 'tag'){
                      $taxonomy = $this->tag_taxonomy;
                      $limit_value = $this->options['tag'];
                  }
                  $root_term_arr = get_terms($taxonomy, $args);
                  if(!empty($limit_value)){
                      $return_arr = array();
                      
                      $limit_arr = explode(',', $limit_value);
                      foreach($root_term_arr as $item) {
                          if(count($limit_arr)>0) {
                              foreach($limit_arr as $key){
                                  if($item->slug == $key) {
                                      $return_arr[] = $item;
                                      break;
                                  }
                              }
                          }
                      }
                  }
                  if(empty($return_arr)){
                      return $root_term_arr;
                  }else {
                      return $return_arr;
                  }
               }
                
                
               function get_ecomm_post_categories( $postID, $full_array = false ){
                        $cats = get_the_terms( $postID, $this->taxonomy);

                        //escape hatch
                        if( !is_array($cats) ){
                                return false;
                        }

                        //return full array
                        if( $full_array ){
                                return $cats;
                        }


                        foreach( $cats as $cat ){
                                $cat_names[] = $cat->name;
                        }

                        return $cat_names;
                }


                function get_ecomm_post_tags( $postID, $full_array = false ){
                    $tags = get_the_terms( $postID, $this->tag_taxonomy);

                    //escape hatch
                    if( !is_array($tags) ){
                            return false;
                    }

                    //return full array
                    if( $full_array ){
                         return $tags;
                    }


                    foreach( $tags as $tag ){
                         $tag_names[] = $tag->name;
                    }

                    return $tag_names;
                }
                
                function get_header_navigation_contents(){
                    $header_contents = '<span class="header_element ecomm_cat cat_current"  alt="'.$this->options['category'].'">See All</span>';
                    $cats_arr  = $this->get_ecomm_all_terms('category');
                    if(is_array($cats_arr) and count($cats_arr)>0) {
                        foreach($cats_arr as $item) {
                            if($item->count >0 ){
                                 $header_contents .="&nbsp;|&nbsp;";  
                                 $header_contents .='<span class="header_element ecomm_cat" alt="'.$item->slug.'">'.$item->name.'</span>';
                            }
                        }
                    }
                    
                    $tags_arr  = $this->get_ecomm_all_terms('tag');
                    if(is_array($tags_arr) and count($tags_arr)>0) {
                        $header_contents .="</br>";
                        $loop_index = 1;
                        foreach($tags_arr as $item) {
                            if($item->count >0 ){
                                if($loop_index > 1){
                                   $header_contents .="&nbsp;|&nbsp;";  
                                 }
                                $header_contents .='<span class="header_element ecomm_tag" alt="'.$item->slug.'">'.$item->name.'</span>';
                                $loop_index ++;
                            }
                        }
                    }
                    return $header_contents;
                }
                
                 
                function share_email_send(){
                      $to = html_entity_decode($_REQUEST['to']);
                      $to_addr = explode(";",$to);
                      
                      $from = html_entity_decode($_REQUEST['from']);
                      $message    = stripslashes($_REQUEST['body_main']);
                      $message   .= '</br>'.stripslashes($_REQUEST['body_attach']);
                      $subject  = "Share with Friend: ".stripslashes($_REQUEST['subject']);
                      $headers = 'From:'.$from."\r\n";
                      $send_check = wp_mail( $to_addr, $subject, $message, $headers );
                      if($send_check){
                        echo json_encode(array('status' => 'success'));
                      } else{
                        echo json_encode(array('status' => 'error'));
                      }
                      die();
                }
                
                
                function query_the_single_post($slug){
                        $post_type = $this->post_type;
                        $query = array("name"=>$slug, 'post_type' => $post_type, 'numberposts' => 1);
			$the_query = new WP_Query($query);
                        if($the_query){
                            return  $the_query->posts;
                        }
                }
                
                function get_no_post_default(){
                    $default ='<div class="panel panel-1" >
                    <span class="panel-title" style="display:none;">default</span>
                       <div class="e_comm_slider_background_image">
                         <a target="_self" href="#"><img class="attachment-full wp-post-image" width="'.ECOMM_DEFAULT_PIC_WIDTH.'" height="'.ECOMM_DEFAULT_PIC_HEIGHT.'" alt="Desert" src="'.plugins_url('/css/images/no_product.png', dirname(__FILE__)).'"></a>
                        </div>
                    </div>';
                    return $default;

                }
                
                function get_single_ecomm_post_for_front_end(){
                    $post_slug = $_REQUEST['index_slug'];
                    $this->posts = $this->query_the_single_post($post_slug);
                    $comm_posts  = $this->posts;
                    //here need to put default image  **********************
                    $slider_main_body = $this->get_no_post_default();

                    $sidebar_part = ""; 
                    if(is_array($comm_posts) and count($comm_posts)>0) {
                        $this->get_slide_contents_parts($comm_posts);
                        $slider_main_body = $this->get_show_slider_main_body_contents();
                        $sidebar_part =  $this->get_ecomm_slider_sidebar_contents();
                    }
                   $content_return = array('status' =>'success','message'=> array('main_body' =>$slider_main_body,'sidebar'=>$sidebar_part)); 
                    //for ajax process
                   echo json_encode($content_return);
                   die();
                }
                
                
                function get_requested_ecomm_posts_for_front_end($init=false){
                      $cat_slug = $_REQUEST['cat_slug'];
                      $tag_slug = $_REQUEST['tag_slug'];
                      $index_slug = $_REQUEST['index_slug'];
                      $offset   = $_REQUEST['offset'];
                      $number_of_page = $_REQUEST['number_of_page'];
                      $show_sidebar   = $_REQUEST['slide_show_sidebar'];
                      $parent_postid  = $_REQUEST['host_postid'];
                      $parent_postslug  = $_REQUEST['host_postslug'];
                      if(isset($_REQUEST['init'])){
                            $init   = $_REQUEST['init'];
                      }
                    //  if ( wp_verify_nonce($_REQUEST['nonce'], ECOMM_VALIDATE_KEY) ) {
                           if(!empty($parent_postslug)){
                                    //add here for from slug get id 
                                    $slug_to_get = $parent_postslug;
                                    global $wpdb;
                                    $the_query = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_name like '".$slug_to_get."'" );
                                    if($the_query !== false){
                                        $parent_postid = $the_query[0]->ID;
                                    }
                                    $options = get_post_meta($parent_postid, $this->option_meta);
                                    
                                    if(empty($options)){
                                        die();
                                    } 
                                    $this->options = $options[0];
                                    $this->parent_postid   = $parent_postid;
                                    $this->parent_postslug = $parent_postslug;
                                    $this->parent_post_link =  get_permalink($parent_postid);
                                 
                            } 
                          
                          if(empty($cat_slug)){
                               $cat_slug = $this->options['category'];
                             
                           }
                           if(empty($offset)){
                               $offset = 0;
                           }
                           
                           $header_nav = '';
                           if($init){
                                $header_nav =  $this->get_header_navigation_contents();
                                $plugin_options = get_option('stjoseph_ecomm_slider_options'); 

                           }
                           $number_of_page = -1;
                           $this->posts = $this->query_the_requested_posts($cat_slug, $tag_slug, $offset, $number_of_page);
                           $comm_posts = $this->posts;
                            //here need to put default image  **********************
                           $slider_main_body = $this->get_no_post_default();
                           $thumbnail_content = $thumbnail_nav = $sidebar_part = ""; 
                            if(is_array($comm_posts) and count($comm_posts)>0) {
                                $index_post = $this->get_slide_contents_parts($comm_posts, $index_slug);
                                if(!empty($index_post)){
                                    $thumbnail_content = $this->get_show_slider_thumbnail_nav_contents();
                                    $thumbnail_nav = $this->get_show_slider_thumbnail_nav();
                                   //$slider_main_body = $this->get_show_slider_main_body_contents();
                                   //$sidebar_part =  $this->get_ecomm_slider_sidebar_contents();
                                    $slider_main_body = $this->get_slider_main_body_contents($index_post["index"],$index_post['post']);
                                    $sidebar_collection[]   = $this->get_slider_sidebar_contents($title, $index_post['post']);
                                    $sidebar_part =  $this->get_ecomm_slider_sidebar_contents($sidebar_collection);
                                }
                            }
                           $content_return = array(
                               'status' =>'success',
                               'message'=>array(
                                   'post_active_index'=>(isset($index_post['index'])? $index_post['index']:1), 
                                   'options'=>$plugin_options,
                                   'header_nav'=>$header_nav, 
                                   'main_body' =>$slider_main_body,
                                   'thumbnail_nav'=>$thumbnail_nav, 
                                   'thumbnail_content'=>$thumbnail_content,
                                   'sidebar'=>$sidebar_part,
                                   'numberofposts'=>$this->options['numberposts'], 
                                   'total_posts'=>$this->slide_total_count)
                                   ); 
                            //for ajax process
                           echo json_encode($content_return);
                           die();
                           
                     // } else {
                     //      echo json_encode(array('status' => 'error', 'message' => 'No Nonce Match'));
                     // }
                     
                }
                
                function get_show_slider_thumbnail_nav(){
                    $number_per_page = intval($this->options['numberposts']);
                    $total_count  = intval($this->slide_total_count);
                    $page_number  = intval(ceil($total_count/$number_per_page));
                    $nav_contents = '';
                    for($i=1;$i<=$page_number;$i++){
                        if($i==1){
                           $nav_contents .="<a class='active nav-".$i."'></a>"; 
                        }else{
                           $nav_contents .="<a class='nav-".$i."'></a>";
                        }
                    }
                    return $nav_contents;
                    
                }
                
                function query_the_requested_posts($cat_slug=NULL, $tag_slug=NULL, $offset=0, $number_of_page = DEFAULT_LOADING_POSTS_NUBMER){
                        $options = $this->options;
                   	// Extract shortcode attributes
			$post_type = $this->post_type;
                       
			// Create an array with default values that we can use to build our query
			$query = array('numberposts' => $number_of_page,'post_status'=>'publish', 'nopaging'=>true,'post_type' => $post_type);
			
			// If the post type is post or the default, set the category based on taxonomy.
			if($cat_slug){
                            $query[$this->taxonomy] = $cat_slug; //slug
                        }
                        
                        if($tag_slug){
                            $query[$this->tag_taxonomy] = $tag_slug; //slug
                        }
                        
                        $query['offset'] = $offset;
			
			// Run query and get posts
			if( !has_filter('stjoseph_ecomm_slider_custom_query_results') ) {
                                $the_query = new WP_Query($query);
                                $promo_posts =  $the_query->posts;
                                $this->slide_total_count =  count($promo_posts);
                               
                                
                        }
                        
                        return $promo_posts;
                }
                
                
                
                
                function get_slide_contents_parts( $ecomm_posts, $index_slug=""){
                    $main_body_contents = '';
                    if(!$ecomm_posts ){ 
                        return "";
                    }
                    $index_post = "";
                    $i =1;
                    foreach($ecomm_posts as $post):
                            $title = $post->post_title;
                            $slug  = $post->post_name;
                            if(!empty($index_slug) and ($slug == $index_slug)){
                                $index_post = array("index"=>$i,"post"=>$post);
                            }
                            $thumb_collection[]     = array("slug"=>$slug,"image"=>$this->get_slider_thumb($title, $post));
                            $sidebar_collection[]   = $this->get_slider_sidebar_contents($title, $post);
                            $main_body_collection[] = $this->get_slider_main_body_contents($i, $post); //leave for future use 
                            $i++;
                     endforeach;
                    $this->main_body_collection = $main_body_collection;        
                    $this->thumb_collection     = $thumb_collection;
                    $this->sidebar_collection   = $sidebar_collection;
                    if(empty($index_post)){
                        $index_post = array("index"=>1,"post"=>$ecomm_posts[0]);
                    }
                    return $index_post;
                    
                }
                
                function get_slider_main_body_contents($i,$post){
                    $title = $post->post_title; 
                    $excerpt = $post->post_excerpt;
                    $postid = $post->ID;
                    $post_slug = $post->post_name;
                    $image = $this->get_slider_image($this->options->id ,$post);
                    extract( $this->fetch_link_settings($post));
                    // Store all the values in an array and pass it to the stjoseph_ecomm_slider_content action
                    $values = compact('title', 'excerpt', 'image', 'destination_url', 'target', 'display_title', 'display_excerpt'); 
                    extract($values);

                    $main_body_contents .= '<div value="'.$i.'" id="'.$post_slug.'" class="panel panel-'.$i.'">';
                    $main_body_contents .= '<span class="panel-title" style="display:none;">'.$title.'</span>';
                    $main_body_contents .= '<div class="e_comm_slider_background_image">';
                    $main_body_contents .= '<a href="'.$destination_url.'" target="'.$target.'">'.$image.'</a>';
                    $main_body_contents .= '</div>';

                   //keep the hidden part when the sidebar not showing 
                    $main_body_contents .= '<div class="e_comm_slider_bottom_bar">';
                    $main_body_contents .= '<div class="bottom_desc">';
                    $main_body_contents .= '<span class="description">'.$post->post_content.'</span>';
                    $main_body_contents .= '</div>';
                    $main_body_contents .= '<div class="bottom_buynow">';
                    $bar_link_arr = $this->fetch_link_settings($post);
                    $main_body_contents .= '<a href="'.$bar_link_arr['destination_url'].'" target="'.$bar_link_arr['target'].'">';
                    $main_body_contents .= '<img src="'.ECOMM_POST_LINKS_IMG_DIR.'/buy-now.png" alt="buy now" />';
                    $main_body_contents .= '</a>';
                    $main_body_contents .= '</div></div>';

                    $main_body_contents .= '</div>'; 
                    
                    return $main_body_contents;                    
                }
               
                //added by George Tang for ecomm, not used yet
                function sharethis_ecomm_button($post_link, $title) {
                    $out="";
                    $widget=get_option('st_widget');
                    $tags=get_option('st_tags');
                    if(!empty($widget)){
                            if(preg_match('/buttons.js/',$widget)){
                                    if(!empty($tags)){
                                            $tags=preg_replace("/\\\'/","'", $tags);
                                            $tags=preg_replace("/<\?php the_permalink\(\); \?>/",$post_link, $tags);
                                            $tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags($title), $tags);
                                            $tags=preg_replace("/{URL}/",$post_link, $tags);
                                            $tags=preg_replace("/{TITLE}/",strip_tags($title), $tags);
                                    }else{
                                            $tags="<span class='st_sharethis' st_title='".strip_tags($title)."' st_url='".$post_link."' displayText='ShareThis'></span>";
                                            $tags="<span class='st_facebook_buttons' st_title='<?php echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span><span class='st_twitter_buttons' st_title='<?php echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span><span class='st_email_buttons' st_title='<?php echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span><span class='st_sharethis_buttons' st_title='<?php echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span><span class='st_fblike_buttons' st_title='<?php echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span><span class='st_plusone_buttons' st_title='<?php  echo $title; ?>' st_url='<?php echo $post_link; ?>' displayText='share'></span>";	
                                            $tags=preg_replace("/<\?php the_permalink\(\); \?>/",$post_link, $tags);
                                            $tags=preg_replace("/<\?php the_title\(\); \?>/",strip_tags($title), $tags);
                                    }
                                    $out=$tags;	
                            }else{
                                    $out = '<script type="text/javascript">SHARETHIS.addEntry({ title: "'.strip_tags($title).'", url: "'.$post_link.'" });</script>';
                            }
                    }

                        echo $out;
                }
                //end 

                function get_show_slider_main_body_contents(){
                   $main_body_contents = "";
                   $collections =  $this->main_body_collection;
                   $main_body_contents = implode(" ",$collections);
                   return $main_body_contents;                   
                }
               
                function get_ecomm_slider_sidebar_contents($sidebar_collections = NULL){
                    $sidebar_contents = ''; 
                    if(empty($sidebar_collections)){
                        $collections =  $this->sidebar_collection;
                    } else {
                        $collections = $sidebar_collections;
                    }
                   // $share_link  = ECOMM_HOME_URL.'/'.$this->post_type.'/#/?host='.$this->parent_postslug;
                    $share_link  = ECOMM_HOME_URL.'/'.$this->post_type.'/%23/?';
                    //$share_link  = ECOMM_HOME_URL.'/'.$this->post_type.'/?';
                   
                    $sidebar_contents .='<div id="e_comm_slider_sidebar_wrapper">';
                         foreach($collections as $key => $sidebar):
                                $link_hash   = $share_link.'slug='.$sidebar['bar_slug'];
                                $current_index = $key + 1;
                        
                                $sidebar_contents .='<div class="sidebar_item  sidebar_item-'.$current_index.'">';
                                $sidebar_contents .='<div class="product_desc">';
                                $sidebar_contents .='<div class="sidebar_buynow">';
                                $sidebar_contents .='<a href="'.$sidebar['bar_link_arr']['destination_url'].'" target="'.$sidebar['bar_link_arr']['target'].'">';
                                $sidebar_contents .='<img src="'.ECOMM_POST_LINKS_IMG_DIR.'/buy-now.png" alt="buy now" />';
                                $sidebar_contents .='</a>';
                                $sidebar_contents .='</div>';
                                $sidebar_contents .='<div class="slide-info">';			
                                //only for CF, comment out this one
                                //$sidebar_contents .='<span class="title">'.$sidebar['bar_title'].'</span>';
                                $sidebar_contents .='<span class="description">'.$sidebar['bar_content'].'</br><a href="'.$sidebar['bar_link_arr']['destination_url'].'" target="'.$sidebar['bar_link_arr']['target'].'">buy this now</a></span>';
                               //$sidebar_contents .='<span class="caption">'.(!empty($sidebar['bar_excerpt']))?$sidebar['bar_excerpt']:''.'</span>';
                                
                                $sidebar_contents .='</div>';
                                $sidebar_contents .='</div>';    
                                $sidebar_contents .='<div class="social-media">';
                                $sidebar_contents .='<iframe src="http://www.facebook.com/plugins/like.php?href='.$link_hash.'&amp;layout=button_count&amp;show_faces=true&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';
                                $sidebar_contents .='<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="'.TWITTER_ACCT.'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
                                $sidebar_contents .='</br>';
                                $sidebar_contents .='<a href="http://pinterest.com/pin/create/button/?url='.$link_hash.'&media='.$sidebar['real_image_url'].'"  class="pin-it-button" count-layout="horizontal"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
                                $sidebar_contents .='&nbsp;<a rel="#email_form" class="email" value="'.$sidebar['image_url'].'" title="'.$sidebar['bar_title'].'" ><img border="0" src="'.ECOMM_POST_LINKS_IMG_DIR.'/email.png'.'" title="Eamil your friend" /></a>';
                                $sidebar_contents .='</div>';
                                $sidebar_contents .='</div>';
                        endforeach;
                    $sidebar_contents .='</div>';
                    return $sidebar_contents;                    
               }
                
               
                
                function get_show_slider_thumbnail_nav_contents(){
                    $thumbnail_nav_contents = '';
                    $options = $this->options;
                   	// Extract shortcode attributes
                    extract( $options );
                    $thumb_collection = $this->thumb_collection;
                    
                    if(is_array($thumb_collection) and count($thumb_collection) > 0) {
                        $values =  array('thumbs' => $thumb_collection);
                        extract($values);
                        $page_count = 1;
                        foreach($thumbs as $key => $thumb): 
                        if($key % $numberposts==0) {
                            $thumbnail_nav_contents .= '<div class="scrollable_div">';
                            $page_count++;
                        }
                        $current_index = $key + 1;
                        $thumbnail_nav_contents .= '<li  rel="'.$thumb["slug"].'"  class="'.$current_index.'">';
                        $thumbnail_nav_contents .= $thumb["image"]; 
                        $thumbnail_nav_contents .= '</li>';
                        if(($key+1) % $numberposts ==0) { // if counter is multiple of , put an closing div 
                            $thumbnail_nav_contents .='</div>';
                         }     
                        endforeach;
                        if(($key+1) % $numberposts !=0) { // if counter is not multiple of , put an closing div 
                            $thumbnail_nav_contents .='</div>';
                         } 
                    }
                    return $thumbnail_nav_contents;		
                }
                                
                
                function short_code_show_slider( $atts ){
                        global $post;
                        $parent_post_id = $post->ID; 
                        $parent_post_slug = $post->post_name;
                        
			// Get plugin options
			$options = $this->get_options();
			// Get combined and filtered attribute list
			$options = shortcode_atts(array('id' => NULL,
                                                        'width' => NULL, 
                                                        'height' => NULL,
                                                        'offset' =>'0',
                                                        'post_type' => $this->post_type, 
                                                        'category' => NULL,
                                                        'tag'      => NULL,
                                                        'numberposts' => $options->numberposts,
                                                       ), $atts);
			// Validate options
			foreach( $options as $option => $value ){
				$options[$option] = $this->validate_options( $option, $value );
                        }
                        
                        //George add here for later use this options 
                        $this->options = $options; 
                        
                        update_post_meta($parent_post_id, $this->option_meta,$options);
                        $cats_slug = $this->options['category'];
                        $tags_slug = $this->options['tag'];
                       // $link_hash_base = '#/?p=1&ecomm_post='.$parent_post_id;                        
                        $link_hash_base = '#/?p=1&host='.$parent_post_slug;                        
                        //George add end 
                        
			
			// Extract shortcode attributes
			extract( $options );
			
			// Create an array with default values that we can use to build our query
			$query = array('numberposts' => $numberposts, 'offset'=>$offset, 'post_type' => $post_type);
			
			// If the post type is post or the default, set the category based on taxonomy.
			if( $category ){
                           $query[$this->taxonomy] = $category;
			}
                        
			// Use the stjoseph_ecomm_slider_query filter to customize the results returned.
			$query = apply_filters('stjoseph_ecomm_slider_query', $query);
			
			// Use the stjoseph_ecomm_slider_query_by_id filter to customize a query for a particular slider.
			$query_by_id = apply_filters('stjoseph_ecomm_slider_query_by_id', array('query' => $query, 'id' => $id));
			$query = $query_by_id['query'];
		
			// Use the stjoseph_ecomm_slider_custom_query_results to run your own query and return the results object to the slider
			$promo_posts = apply_filters('stjoseph_ecomm_slider_custom_query_results', $promo_posts);
		
			// Run query and get posts
			if( !has_filter('stjoseph_ecomm_slider_custom_query_results') ) {
                                $the_query = new WP_Query($query);
                                $this->slide_total_count =  $the_query->found_posts;
                                $promo_posts =  array_slice($the_query->posts,0,$numberposts);
                        }
                        
			// If there are results, build slider.  Otherwise, don't show anything.
			if( $promo_posts ){
                                // Initiate iteration counter
				$i = 1;
				// Begin Output
				ob_start(); ?>
                                <div id="ecomm_slider_thumbnail">
                                    <ul class="thumbnail_area">
                                    <?php foreach($promo_posts as $post): setup_postdata($post);
                                            // Get the title
                                            $title = get_the_title(); 
                                           
                                            // Fetch thumbnails for slider nav, if thumbnail nav is being used
                                            $thumb_image = $this->get_slider_thumb( $title );
                                            
                                            $link_hash = $link_hash_base.'&slug='.$post->post_name;
                                       ?>
                                           <li class="<?php echo $i; ?>">
                                               <a href="<?php echo ECOMM_HOME_URL.'/'.$this->post_type.'/'.$link_hash; ?>" target="_blank" class="popup" ><?php echo $thumb_image; ?></a>
                                      
                                           </li>
                                            <?php $i++;?>
                                    <?php endforeach; ?>
                                    </ul>    
                                </div>    
                                <?php
                                // Reset query so that comment forms work properly
				wp_reset_query();
		  
				// End Output		  
				return ob_get_clean();
		
			}
		}                
                
	}
  
}
?>