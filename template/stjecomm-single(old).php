<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}
/**
 * ecomm-single.php - The Template for displaying all ecomm sliders.
 *
 * @package 
 * @subpackage templates
 * @author George Tang
 * @copyright 2012-2015
 * @access public
 * @since 1.0
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta content="width=1024px;" name="viewport"/>
<title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php 
 global $gl_comm_sliders;
 $gl_comm_sliders->enqueue_scripts();
wp_head(); 
   
?>
<!--[if IE]>
 <style>   
 .e_comm_slider img {
    margin:0; 
    padding:0; 
    background-color: #fff;
    height: 565px;
    border:none;
   </style>                                  
<![endif]-->

</head>
    
<?php
/*
$slug_to_get = $_GET['slug'];
$args=array(
  'name' => $slug_to_get,
  'post_type' => 'post',
  'numberposts' => 1,
);

$the_query = new WP_Query($args); 
$my_posts = $the_query->posts;

if( $my_posts ) {
   $parentpost_id = $my_posts[0]->ID;
   $_GET['parent_postid'] = $parentpost_id;    
}

$options = get_option('stjoseph_ecomm_slider_options'); 
global $gl_comm_sliders;

$_REQUEST['host_postid'] = $parentpost_id;
$_REQUEST['host_postslug'] = $slug_to_get;
$init_page_contents_arr = $gl_comm_sliders->get_requested_ecomm_posts_for_front_end(true);
$init_page_contents = $init_page_contents_arr['message'];
$number_per_page    = $init_page_contents['numberofposts'];
$total_count        = $init_page_contents['total_posts'];
$last_page_number   = intval(ceil(intval($total_count)/intval($number_per_page)));

$header_nav         = $init_page_contents['header_nav']; 
$slider_main_body   = $init_page_contents['main_body'];
$thumbnail_nav      = $init_page_contents['thumbnail_nav'];
$thumbnail_content  = $init_page_contents['thumbnail_content'];
$sidebar_part       = $init_page_contents['sidebar'];
*/
ob_start();  
?>
<body id="ecomm_slideshow">
   <input type="hidden" value="<?php echo $parentpost_id; ?>" id="slide_parent_postid"/>
   <input type="hidden" value="<?php echo $slug_to_get; ?>" id="slide_parent_slug"/>
   <input type="hidden" value="<?php echo $number_per_page; ?>" id="slide_count_per_page"/>
   <input type="hidden" value="<?php echo $total_count; ?>" id="slide_total_count"/>
   <input type="hidden" value="<?php echo $last_page_number;?>" id="slide_last_page"/>
   
   <input type="hidden" value="" id="slide_show_sidebar"/>
   <input type="hidden" value="<?php echo wp_create_nonce(ECOMM_VALIDATE_KEY); ?>" id="security" name="security"/>

   <div id="slide_page"  class="slide_default_page"><!--start #page-->
         <div id="slider_contents">
               <div class="close"><a>CLOSE</a></div>
               <div class="clear"></div> 

                   
             <div id="left_side"  class="left_side_auto">
                   <div id="e_comm_slider_leadingboard">
                    <img alt="advertisement" src="<?php echo plugins_url('/stjoseph_ecomm'); ?>/css/images/advertisment.gif" />   
                    <?php echo $options['leaderboard_code']; ?>   
                   </div>
                   <div id="e_comm_slider_brand_header">
                        <div id="e_comm_slider_brand">
                        </div>
                        <div id="e_comm_slider_gift_guide">
                        </div>
                    </div>
                   <div class="clear"></div> 

                   <div id="slider_body">
                       
                         <div id="e_comm_slider_header">
                            <?php echo $header_nav; ?> 
                          </div>
                           
                          <div id="loading">
                          </div>
                       
                          <div class="header_show_post_number bottom_arrow">
                                   <span title="Move Backward" class="ecomm_left_arrow move_backward pointer">&nbsp;</span>
                                   <span class="number_area"></span>
                                   <span title="Move Forward" class="ecomm_right_arrow move_forward pointer">&nbsp;</span>
                          </div>
                          
               		  <div class="e_comm_slider_wrapper first thumb_nav" >
                               
                                <div class="image_body">
                                    <a id="image_a_left" href=""><span class="move_backward pointer image_shade_left"></span></a>
                                    <div class="e_comm_slider"> 
                                       <?php  echo $slider_main_body; ?> 
                                    </div>
                                    <a id="image_a_right" href=""><span title="Move Forward" class="move_forward pointer image_shade_right"></span></a>    
                                </div>   
                                <div class="navi">
                                    <?php echo $thumbnail_nav; ?>
                                    <a class="active nav-1" style="display: block;"></a>
                                    <a class="nav-2" ></a>
                                    <a class="nav-3" ></a>
                                    <a class="nav-4" ></a>
                                    <a class="nav-5" ></a>
                                    <a class="nav-6" ></a>
                                    <a class="nav-7" ></a>
                                    <a class="nav-8" ></a>
                                    <a class="nav-9" ></a>
                                    <a class="nav-10"></a>
                                    <a class="nav-11" style="display: block;"></a>
                                    <a class="nav-12" ></a>
                                    <a class="nav-13" ></a>
                                    <a class="nav-14" ></a>
                                    <a class="nav-15" ></a>
                                    <a class="nav-16" ></a>
                                    <a class="nav-17" ></a>
                                    <a class="nav-18" ></a>
                                    <a class="nav-19" ></a>
                                    <a class="nav-20"></a>
                                    
                                </div>
                                <div class="e_comm_slider_thumb_nav" >
                                    <a class="prev browse left pointer"></a>
                                    <div class="scrollable" id="slide_thumb_nav">
                                        <div class="thumb_nav items slider_selections">
                                          <?php  echo $thumbnail_content;  ?>  
                                        </div>
                                    </div>
                                    <a class="next browse right pointer"></a>
                                   
                                </div>
                               
                                    
			   </div>
                      </div> <!-- End for slide body -->
                  </div><!-- End for left --> 
                  <div id="slider_sidebar">
                    <div id="e_comm_slider_sidebar_wrapper">
                       <div id="sidebar_adv_top" class="ecomm-box-ad">
                       <img alt="advertisement" src="<?php echo plugins_url('/stjoseph_ecomm'); ?>/css/images/advertisment.gif" />   
                       <?php echo $options['sidebar_top_code']; ?>   
                       </div>
                       <div class="header_show_post_number">
                         <span title="Move Backward" class="ecomm_left_arrow move_backward pointer">&nbsp;</span>
                         <span class="number_area"></span>
                         <span title="Move Forward" class="ecomm_right_arrow move_forward pointer">&nbsp;</span>    
                        </div>  
                    
                       <div id="sidebar_body">
                         <?php echo $sidebar_part;?> 
                       </div>
                       
                       <div id="sidebar_adv_bottom" class="ecomm-box-ad">
                        <img alt="advertisement" src="<?php echo plugins_url('/stjoseph_ecomm'); ?>/css/images/advertisment.gif" />   
                        <?php echo $options['sidebar_bottom_code']; ?>   
                       </div>
                    </div>
                      
                    <div id="email_form"> <!-- for overlay -->
                       <?php include('stjecomm-email-form.php'); ?>
                    </div>    
                    
                      
                   </div>    
              </div>                
              
    </div> <!--End for page -->     
    
</body>    
</html>
<?php 
ob_flush();
?>