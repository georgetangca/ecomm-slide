<?php
require( '../wp-load.php' );

if ( ! defined( 'ECOMM_PLUGIN_NAME' ) ){
  define( 'ECOMM_PLUGIN_NAME', 'stjoseph_ecomm');
}

if ( ! defined( 'ECOMM_PLUGIN_DIR' ) ){
    define( 'ECOMM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . ECOMM_PLUGIN_NAME);
}

include (ECOMM_PLUGIN_DIR.'/template/stjecomm-single.php');    
?>