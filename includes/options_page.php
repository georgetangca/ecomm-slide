<div class="wrap">
	
	<h2><?php _e('Ecomm Slider Options', 'stjoseph_ecomm'); ?></h2>
	<p><?php _e('The options below will change the settings for all of the Ecomm Sliders running on your website.', 'stjoseph_ecomm'); ?></p>
	
	<div class="postbox-container" style="width:70%;">
	
		<div class="metabox-holder">
		
			<div class="meta-box-sortables ui-sortable">
			
				<form method="post" action="options.php">
					<?php settings_fields( 'stjoseph_ecomm_slider-settings-group' ); ?>
					<?php $options = get_option('stjoseph_ecomm_slider_options'); ?>

					<div id="slider-settings" class="postbox">
						<div class="handlediv" title="Click to toggle"><br /></div>
						
						<h3 class="hndle"><span><?php _e('Slider Settings', 'stjoseph_ecomm'); ?></span></h3>
						
						<div class="inside">
						
							<p>
                                                                <input type="checkbox" name="stjoseph_ecomm_slider_options[leadboard_show]" value="true" <?php if( $options['leadboard_show']) echo 'checked="checked"'; ?> />
								<label><?php _e('Show Leadbord Adv', 'stjoseph_ecomm'); ?></label><br />
								<strong><?php _e('Leader Board Code:', 'stjoseph_ecomm'); ?></strong><br />
								<p><textarea name="stjoseph_ecomm_slider_options[leaderboard_code]" rows="10" style="width:100%;"><?php echo $options['leaderboard_code']; ?></textarea></p>
                                                    	</p>
					
						</div>
                                                
                                                <div class="inside">
						
							<p>
                                                                <input type="checkbox" name="stjoseph_ecomm_slider_options[sidebar_top_show]" value="true" <?php if( $options['sidebar_top_show']) echo 'checked="checked"'; ?> />
								<label><?php _e('Show Sidebar Top Adv', 'stjoseph_ecomm'); ?></label><br />
								<strong><?php _e('Adv Code:', 'stjoseph_ecomm'); ?></strong><br />
								<p><textarea name="stjoseph_ecomm_slider_options[sidebar_top_code]" rows="10" style="width:100%;"><?php echo $options['sidebar_top_code']; ?></textarea></p>
                                                    	</p>
					
						</div>
                                                
                                                <div class="inside">
						
							<p>
                                                                <input type="checkbox" name="stjoseph_ecomm_slider_options[sidebar_bottom_show]" value="true" <?php if( $options['sidebar_bottom_show']) echo 'checked="checked"'; ?> />
								<label><?php _e('Show Sidebar Bottom Adv', 'stjoseph_ecomm'); ?></label><br />
								<strong><?php _e('Adv Code:', 'stjoseph_ecomm'); ?></strong><br />
								<p><textarea name="stjoseph_ecomm_slider_options[sidebar_bottom_code]" rows="10" style="width:100%;"><?php echo $options['sidebar_bottom_code']; ?></textarea></p>
                                                    	</p>
					
						</div>
						
					</div>
					
					<div class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'stjoseph_ecomm') ?>" />
					</div>
			  
				</form>
			
			</div> 
		
		</div>
  
	</div>
	
	
</div>