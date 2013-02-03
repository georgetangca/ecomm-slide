<?php 
global $post;
wp_nonce_field( 'ecomm_update_meta', 'ecomm_slider_noncename' ); 

// Setup form data
$url = get_post_meta($post->ID, '_e_comm_slider_url', TRUE);

// Shortcut variables
$selected = ' selected="selected"';
$checked = ' checked="checked"';
?>
<h3><?php _e('Buy Now Linking', 'stjoseph_ecomm'); ?></h3>
  <p>
    <label for="_e_comm_slider_url"><?php _e('URL: ', 'stjoseph_ecomm') ?></label> 
    <input type="text" id= "_e_comm_slider_url" name="_e_comm_slider_url" value="<?php if(!empty($url)) echo $url; ?>" size="75" />
    <label for="_e_comm_slider_url"><?php _e('Put the "Buy Now" link URL', 'stjoseph_ecomm') ?></label> 
  </p>

<!-- Disable e_commtion Page --><br />
 
