<?php
/*
Plugin Name: stJoseph eComm Slider
Plugin URI: 
Description: 
Version: 1.0
Author: George
Author URI: 
License: GPL3
*/

// Check for PHP 5+ and return error before getting to code that will throw errors
if( phpversion() < 5 )
	die( printf(__('Sorry, this plugin requires PHP version %s or later. You are currently running version %s.', 'stjoseph_ecomm'), 5, phpversion()) );

// DEFINE CONSTANTS
if( !defined('STJOSEPH_ECOMM_SLIDER_VER') ) define( 'STJOSEPH_ECOMM_SLIDER_VER', '3.3.4' );



if ( ! defined( 'ECOMM_PLUGIN_BASENAME' ) )
	define( 'ECOMM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'ECOMM_PLUGIN_NAME' ) )
	define( 'ECOMM_PLUGIN_NAME', trim(dirname(ECOMM_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'ECOMM_PLUGIN_DIR' ) )
	define( 'ECOMM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . ECOMM_PLUGIN_NAME);

if( !defined('ECOMM_VALIDATE_KEY') ) define( 'ECOMM_VALIDATE_KEY', 'ecomm_key' );

if(!defined('ECOMM_POST_LINKS_IMG_DIR')){
   define('ECOMM_POST_LINKS_IMG_DIR', plugins_url('/css/images', __FILE__));     
}

if(!defined('ECOMM_TEMPLATE_DIR')){
   define('ECOMM_TEMPLATE_DIR', ECOMM_PLUGIN_DIR.'/template');     
}

if(!defined('ECOMM_DEFAULT_PIC_WIDTH')){
   define('ECOMM_DEFAULT_PIC_WIDTH', "100%");     
}

if(!defined('ECOMM_DEFAULT_PIC_HEIGHT')){
   define('ECOMM_DEFAULT_PIC_HEIGHT', "auto");     
}

if(!defined('ECOMM_MAX_PIC_WIDTH')){
   define('ECOMM_MAX_PIC_WIDTH', "800");     
}

if(!defined('ECOMM_MAX_PIC_HEIGHT')){
   define('ECOMM_MAX_PIC_HEIGHT', "565");     
}

/*
if(!defined('ECOMM_DEFAULT_THUMBNAIL_WIDTH')){
   define('ECOMM_DEFAULT_THUMBNAIL_WIDTH', "auto");     
}

if(!defined('ECOMM_DEFAULT_THUMBNAIL_HEIGHT')){
   define('ECOMM_DEFAULT_THUMBNAIL_HEIGHT', "100%");     
}
*/
if(!defined('ECOMM_DEFAULT_THUMBNAIL_WIDTH')){
   define('ECOMM_DEFAULT_THUMBNAIL_WIDTH', "140");     
}

if(!defined('ECOMM_DEFAULT_THUMBNAIL_HEIGHT')){
   define('ECOMM_DEFAULT_THUMBNAIL_HEIGHT', "140");     
}

if(!defined('ECOMM_MAX_THUMBNAIL_WIDTH')){
   define('ECOMM_DEFAULT_THUMBNAIL_WIDTH', "140");     
}

if(!defined('ECOMM_MAX_THUMBNAIL_HEIGHT')){
   define('ECOMM_DEFAULT_THUMBNAIL_HEIGHT', "140");     
}


if(!defined('DEFAULT_LOADING_POSTS_NUBMER')){
   define('DEFAULT_LOADING_POSTS_NUBMER','5');     
}

if(!defined('ECOMM_HOME_URL')){
   define('ECOMM_HOME_URL',home_url());     
}

// INCLUDE FILES
include( dirname(__FILE__).'/classes/ecomm_post_type.php' );
include( dirname(__FILE__).'/classes/ecomm_register_tax.php' );
include( dirname(__FILE__).'/classes/ecomm_slider.php' );
include( dirname(__FILE__).'/classes/ecomm_admin.php' );

if( class_exists('e_comm_slider') ){
    global $gl_comm_sliders;
    $gl_comm_sliders = new e_comm_slider();
}
if(is_admin() && class_exists('e_comm_admin') ){
     new e_comm_admin();
 }    


?>