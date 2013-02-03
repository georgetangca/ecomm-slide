// Function returns a random number between 1 and n
function rand(n){return(Math.floor(Math.random()*n+1));}

jQuery.noConflict();

//Only allow one eComm slide exists on the same page
jQuery(document).ready(function(jQuery){
  if(jQuery('#security').length>0) { //the slide show detail page 
      
    var sliders,panelCount,thisSlider,panels,currentSlider,initialPanel;
  
    init_process(); 
    var autoAdvance = false;
    var navOption = 'thumb';
    //here disable this auto 
    var slide_number_of_page  = parseInt(jQuery('#slide_count_per_page').val());
    var slide_total_count     = parseInt(jQuery('#slide_total_count').val());
    var slide_show_sidebar    = parseInt(jQuery('#slide_show_sidebar').val());
    
    var last_page =  parseInt(jQuery('#slide_last_page').val());
    
    var showsidebar = 1;
    var jQuerywin_width =  jQuery(window).width();
    if(jQuerywin_width < 1150){
          if(jQuery('#slide_page').hasClass('slide_default_page')){
              jQuery('#slide_page').removeClass('slide_default_page')
          }
          if(jQuery('#left_side').hasClass("left_side_auto")){
              jQuery('#left_side').removeClass("left_side_auto")
          };
         jQuery('#slide_page').addClass('slide_auto_page')
         jQuery('#left_side').addClass("left_side_width_800");
   }    
    
    resizedw();
    
    jQuery("#slide_thumb_nav").scrollable({onSeek:seek_callback_fun}).navigator();
    var api = jQuery("#slide_thumb_nav").data("scrollable");
    //api.getConf().circular = true;
   /* refresh_sliders();*/      
}  

function init_process(){ 
     var parent_slug = jQuery.address.parameter('host');
     jQuery("#slide_parent_slug").val(parent_slug);
     init_sliders();
}   
  


function seek_callback_fun(obj,current_index){
    jQuery.address.parameter('p', current_index+1);
}
   
    // Create click functions for navigational elements
    jQuery('div#slider_contents div.close').live('click',function(){
       if ( jQuery.browser.msie || jQuery.browser.opera || jQuery.browser.mozilla) window.close(); 
        else window.self.close(); //for chrome 
       //window.close(); 
    });
  
   jQuery('.move_forward').live('click',function(e){
      e.preventDefault();
      progress('forward', currentSlider, panelCount);
      
    });
    jQuery('.move_backward').live('click',function(e){
      e.preventDefault();
      progress('backward', currentSlider, panelCount);
    });
    
    jQuery('.ecomm_thumb_left_arrow').live('click',function(e){
        var api = jQuery("#slide_thumb_nav").data("scrollable");
        var cur_page_number = parseInt(jQuery.address.parameter('p'));
        if(cur_page_number == '' || cur_page_number==null){
              cur_page_number = 1;
        }
        
        if(cur_page_number==1){
            api.end();
        }
      
    });
    jQuery('.ecomm_thumb_right_arrow').live('click',function(e){
       var api = jQuery("#slide_thumb_nav").data("scrollable");
        var cur_page_number = parseInt(jQuery.address.parameter('p'));
        if(cur_page_number == '' || cur_page_number==null){
              cur_page_number = 1;
        }
        
        if(cur_page_number== last_page){
            api.begin();
        }
      
    });
    
    
  
    var doit;
    jQuery(window).resize(function(){
      clearTimeout(doit);
      doit = setTimeout(function(){resizedw();}, 200);
    });

   function resizedw(){
      // alert('come here');
       var jQuerywin_width =  jQuery(window).width();
       
       if(jQuerywin_width < 1000 && showsidebar) {
          showsidebar = 0;
          jQuery('#slider_sidebar').hide();
         
          if(jQuery('#left_side').hasClass('left_side_auto')){
            jQuery('#left_side').removeClass('left_side_auto')  
          }
          jQuery('#left_side').addClass("left_side_width_800");
          
          if(jQuery('#slide_page').hasClass('slide_default_page')){
            jQuery('#slide_page').removeClass('slide_default_page')  
          }
          jQuery('#slide_page').addClass("slide_auto_page");
          
          
          if(jQuery('#image_a_left span').hasClass('image_shade_left')){
            jQuery('#image_a_left span').removeClass('image_shade_left')  
          }
          
          if(jQuery('#image_a_right span').hasClass('image_shade_right')){
            jQuery('#image_a_right span').removeClass('image_shade_right')  
          }
          
          jQuery('.e_comm_slider_bottom_bar').show(); 
          jQuery('.bottom_arrow').show(); 
          
          
          
       } else if(jQuerywin_width >= 1000 && showsidebar==0){
           showsidebar = 1;
          jQuery('#slider_sidebar').show();
          refresh_slides_sidebars();
          jQuery('.e_comm_slider_bottom_bar').hide(); //need to check 
          jQuery('.bottom_arrow').hide();
          if(jQuery('#left_side').hasClass('left_side_width_800')){
            jQuery('#left_side').removeClass('left_side_width_800')  
          }
          jQuery('#left_side').addClass("left_side_auto");
          
          jQuery('.left_side').attr( 'id', '' );
          
          if(jQuery('#slide_page').hasClass('slide_auto_page')){
            jQuery('#slide_page').removeClass('slide_auto_page')  
          }
          jQuery('#slide_page').addClass("slide_default_page");
          
          
          jQuery('#image_a_left span').addClass('image_shade_left')  
          jQuery('#image_a_right span').addClass('image_shade_right')  
          
       }      
   }
    
    
    jQuery('.image_shade_left').hover(
    function() {
       jQuery('.image_shade_left').addClass("ecomm_image_left_arrow");
    },
    function () {
       jQuery('.image_shade_left').removeClass("ecomm_image_left_arrow");
      }
   );

    jQuery('.image_shade_right').hover(
    function() {
        jQuery('.image_shade_right').addClass("ecomm_image_right_arrow");
    },
    function () {
        jQuery('.image_shade_right').removeClass("ecomm_image_right_arrow");
      }
   );    
  
       // Set click functions for each span in the slider nav
  jQuery('.slider_selections div li').live('click',function(){ 
      if( !jQuery(this).hasClass('current_sj') ) {
          progress(jQuery(this).attr('class'), currentSlider, panelCount);
      }         
  });

 jQuery(".ecomm_cat").live('click',function(){
          jQuerycat_slug = jQuery(this).attr('alt');
          jQuery("div#e_comm_slider_header .header_element").removeClass("cat_current");
          jQuery(this).addClass("cat_current");
          jQuery.address.parameter('cat', jQuerycat_slug);
          
          if(jQuerycat_slug ==""){ //click all
               jQuery("div#e_comm_slider_header .header_element").removeClass("tag_current");
               jQuery.address.parameter('tag', "");
          }
          jQuery.address.parameter('p', '1');
          get_requested_ecomm_posts();
      });


      jQuery(".ecomm_tag").live('click',function(){
          jQuerytag_slug = jQuery(this).attr('alt');
          jQuery("div#e_comm_slider_header .header_element").removeClass("tag_current");
          jQuery(this).addClass("tag_current");
          jQuery.address.parameter('tag', jQuerytag_slug);
          jQuery.address.parameter('p', '1');
          get_requested_ecomm_posts();
      });

  // Progress to selected panel
  function progress(value, currentSlider, panelCount){
          var slide_number_of_page  = parseInt(jQuery('#slide_count_per_page').val());
          var slide_total_count     = parseInt(jQuery('#slide_total_count').val());
          var refresh_nav = false;
          
          // Find number of current panel
	  var currentValue = jQuery('div.e_comm_slider > .panel', currentSlider).index(jQuery('div.panel.current_sj', currentSlider)) + 1;
          var cur_page_number = parseInt(jQuery.address.parameter('p'));
          if(cur_page_number == '' || cur_page_number==null){
              cur_page_number = 1;
          }
          
	  // Set value of new panel
  	  if(value == 'forward'){
		var newValue = currentValue + 1;
		if(newValue > panelCount){
                    newValue = 1;
                    
                    if(cur_page_number !== 1){
                        new_page = 1;
                        jQuery.address.parameter('p', '1'); 
                        refresh_nav = true;
                    }
                }else {
                    new_page = Math.ceil(newValue/slide_number_of_page); 
                    if(new_page != cur_page_number){
                        jQuery.address.parameter('p', new_page);
                        refresh_nav = true;
                    }                    
                }
	  } else if(value == 'backward'){
               var newValue = currentValue - 1;
	       if(newValue == 0){
                    newValue = slide_total_count;
                    if(cur_page_number != last_page){
                       new_page = last_page;
                       jQuery.address.parameter('p',last_page); 
                       refresh_nav = true;
                    }                       
               } else {
                  new_page = Math.ceil(newValue/slide_number_of_page);
                   if(new_page != cur_page_number){
                       jQuery.address.parameter('p', new_page);
                       refresh_nav = true;
                   }
               }               
	  }
	  else{
		var newValue = value;
                new_page = Math.ceil(newValue/slide_number_of_page);
                if(new_page != cur_page_number){
                     jQuery.address.parameter('p', new_page);
                     refresh_nav = true;
                }
	  }
	  
	  // Assign variables for ease of use
	  var currentItem = jQuery('.panel-' + currentValue, currentSlider);
	  var newItem = jQuery('.panel-' + newValue, currentSlider);
          var newslug = newItem.attr("id");
         
	  
          var currentSidebar = jQuery('.sidebar_item-' + currentValue);
	  var newSidebarItem = jQuery('.sidebar_item-' + newValue);
	  
          var currentSpan = jQuery('.slider_selections div li.current_sj', currentSlider);
	  var newSpan = jQuery('.slider_selections div li.' + newValue, currentSlider);
          
          
	  
	  // Add / Remove classes
	  currentItem.removeClass('current_sj');
	  newItem.addClass('current_sj');
          
          currentSidebar.removeClass('current_sj');
	  newSidebarItem.addClass('current_sj');          
          
	  currentSpan.removeClass('current_sj');
	  newSpan.addClass('current_sj');
	  
	  // Fade in / out
	  currentItem.fadeOut('fast', function(){
		newItem.fadeIn('fast');
                jQuery.address.parameter('slug', newslug);
	  });
          
          currentSidebar.fadeOut('fast', function(){
		newSidebarItem.fadeIn('fast');
	  });
          
          if(refresh_nav){
              var api = jQuery("#slide_thumb_nav").data("scrollable");
              api.seekTo(new_page-1);
              
          }
        
          update_slider_number_area(newValue);
  }
  
  function update_slider_number_area(current_number){
      var slide_total_count     =  parseInt(jQuery('#slide_total_count').val());
      var slide_number_of_page  = parseInt(jQuery('#slide_count_per_page').val());
      if(slide_total_count ==0 ){
         current_number = 0;  
      } 
      
      
      var offset = parseInt(current_number);
      var jQuerynumber_show_txt = 'Gift '+offset+' OF '+slide_total_count; 
      jQuery('.number_area').html(jQuerynumber_show_txt);        
  }
  
  
 function init_sliders(){
     get_requested_ecomm_posts(1);
 }  

 
 function refresh_sliders(init){
    if(init){
        var cat_slug = jQuery.address.parameter('cat');
        if(cat_slug !="" && cat_slug != null){
         jQuery("div#e_comm_slider_header .header_element").removeClass("cat_current");
         jQuery("span[alt='"+cat_slug+"']").addClass("cat_current");
        }

        var tag_slug = jQuery.address.parameter('tag');
        if(tag_slug !="" && tag_slug != null){
            jQuery("div#e_comm_slider_header .header_element").removeClass("tag_current");
            jQuery("span[alt='"+tag_slug+"']").addClass("tag_current");
        }   
    } 
    refresh_slides_body();
    refresh_navi();
    refresh_slides_sidebars();
  
}

function refresh_slides_body(){
      // Get all instances of e_comm_slider on the page
   sliders = jQuery('.e_comm_slider_wrapper');
  
  // Cycle through each slider
   jQuery.each(sliders, function(){
	// Define current slider
	currentSlider = jQuery(this);
	thisSlider = jQuery('.e_comm_slider', currentSlider);
	
	// Get all panels
        panels = jQuery('.panel', thisSlider);
    	
	// Get total count of panels
        panelCount = panels.length;
	
	// Set number for first panel
	var initialSlug = jQuery.address.parameter('slug');
        if(initialSlug != null && initialSlug !='' && jQuery('#'+initialSlug).length){ //assign the first active picture
           initialPanel = parseInt(jQuery('#'+initialSlug).attr('value'));
        } else {
            initialPanel = 1;
            initialSlug = jQuery(".panel-1").attr("id");
            jQuery.address.parameter('slug',initialSlug);
        }
	// Should we pause the slider on mouseover?
	var pauseOnMouseover;
	if( currentSlider.hasClass('pause') ){ 
            pauseOnMouseover = true;
        }
	else {
            pauseOnMouseover = false;
        }
	
	
	// Hide all panels
	panels.hide();
	
	// Show initial panel and add class 'current' to active panel
	jQuery('.panel-' + initialPanel, currentSlider).show().addClass('current_sj');
        
        update_slider_number_area(initialPanel); 
      
	jQuery('.slider_selections div li[class=' + initialPanel + ']').addClass('current_sj');
	 
	
  }); //end go through each slider 
}

function refresh_slides_sidebars(){
      // Get all instances of e_comm_slider on the page
  sidebars = jQuery('#sidebar_body');
  jQuery("div.social-media a[rel]").overlay();
  /* overlay_api = jQuery("div.social-media a[rel]").data("overlay");*/
   
  // Cycle through each slider
  jQuery.each(sidebars, function(){
	// Define current slider
	currentSidebar = jQuery(this);
	sidebars = jQuery('.sidebar_item', currentSidebar);
	// Hide all panels
	sidebars.hide();
	
	// Show initial panel and add class 'current' to active panel
	jQuery('.sidebar_item-' + initialPanel, currentSidebar).show().addClass('current_sj');
       
	
  }); //end go through each sidebars           
}

  function refresh_navi(){
    jQuery("div.navi a").hide();
    var last_page = parseInt(jQuery('#slide_last_page').val());
    for(var i=1; i<=last_page; ++i){
       jQuery("div.navi a.nav-"+i).show();
    }
    
    
    var api = jQuery("#slide_thumb_nav").data("scrollable");
    new_page = Math.ceil(initialPanel/slide_number_of_page); 
    current_index  = api.getIndex();
   
    api.begin();    
    /*
    if(new_page != (current_index+1)){
       api.seekTo(new_page-1); 
    } 
    */
  } 
  
  function get_requested_ecomm_posts(jQueryinit) {
     jQueryinit = (typeof jQueryinit == 'undefined')? '0' : '1'; 
      
     jQuery("#loading").show();
     jQuery('.image_body').hide();
    // jQuery('#slider_sidebar').hide();
     
     nonce = jQuery('#security').val();
     var jQuerycat_slug = jQuery.address.parameter('cat');
     var jQuerytag_slug = jQuery.address.parameter('tag');
     var jQuerypage_number = jQuery.address.parameter('p');
     var host_postid = jQuery('#slide_parent_postid').val();
     var host_postslug = jQuery('#slide_parent_slug').val();
     
     if(!jQueryinit){
        jQuery.address.parameter('slug',"");
     }
     if(jQuerypage_number == '' || jQuerypage_number==null){
         jQuerypage_number = 1;
     }
     var jQueryoffset = 0;
     if(jQueryinit=='0'){
        jQueryoffset = (parseInt(jQuerypage_number)-1) * parseInt(jQuery('#slide_count_per_page').val())
     }
     var slide_number_of_page  = parseInt(jQuery('#slide_count_per_page').val());
      
     jQuery.post(ECOMM_AJAX.ajaxurl, {
          action: 'get_requested_ecomm_posts_for_front_end',
          host_postid:host_postid, 
          host_postslug:host_postslug, 
          cat_slug: jQuerycat_slug,
          tag_slug: jQuerytag_slug,
          offset:   jQueryoffset,
          number_of_page: slide_number_of_page,
          show_sidebar: slide_show_sidebar,
          init: jQueryinit, //if init even bring the header
          nonce: nonce
     },

     function(message, status) {
          jQuery("#loading").hide();
           
          if (status == 'success') {
               var results = eval('(' + message + ')'); //become object

               if (results.status != 'error') {
                    slide_total_count = parseInt(results.message.total_posts);
                    last_page = Math.ceil(slide_total_count/slide_number_of_page);
                    jQuery('#slide_last_page').val(last_page);
                    
                    jQuery('#slide_total_count').val(slide_total_count);
                    jQuery('div.e_comm_slider').html(results.message.main_body);
                    jQuery('div.slider_selections').html(results.message.thumbnail_content);
                    jQuery('div#sidebar_body').html(results.message.sidebar); 
                   
                   if(jQueryinit == '1'){
                        jQuery('div #e_comm_slider_header').html(results.message.header_nav);
                        jQuery('#slide_count_per_page').val(results.message.numberofposts);
                        var options = results.message.options;
                        if(options.leadboard_show=="true"){
                            jQuery("#e_comm_slider_leadingboard").append(options.leaderboard_code)
                        } else {
                            jQuery("#e_comm_slider_leadingboard").remove();
                        }
                        
                        if(options.sidebar_top_show=="true"){
                            jQuery("#sidebar_adv_top").append(options.sidebar_top_code)
                        } else {
                            jQuery("#sidebar_adv_top").remove();
                        }
                        
                        if(options.sidebar_bottom_show=="true"){
                            jQuery("#sidebar_adv_bottom").append(options.sidebar_bottom_code)
                        } else {
                            jQuery("#sidebar_adv_bottom").remove();
                        }
                        
                    }
                    
                    //jQuery('div.navi').html(results.message.thumbnail_nav);
                    //especially process for navi
                    
                   refresh_sliders(jQueryinit);
                   jQuery('.image_body').show();
                   if(results.message.thumbnail_content == "") {
                       jQuery('div.e_comm_slider_thumb_nav').hide();
                    } else{
                       jQuery('div.e_comm_slider_thumb_nav').show();
                    } 
                   if(slide_total_count==0){
                      jQuery('#slider_sidebar').hide();
                   }else{
                      jQuery('#slider_sidebar').show();
                   }
                   
               }
          } else { //error case
              slide_total_count = 0;
              last_page = 0;              
          }
     }
     );

     return false;
}
  
//for email share
function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

jQuery("#shareDetails").keypress(function() {
    if(!jQuery("#erroCheckMsg").hasClass('sts-dn')){
       jQuery("#erroCheckMsg").addClass('sts-dn').html("");
    }
    
    if(!jQuery("#errorScreen").hasClass('sts-dn')){
       jQuery("#errorScreen").addClass('sts-dn').html("");
    }
    
    if(!jQuery("#doneScreen").hasClass('sts-dn')){
       jQuery("#doneScreen").addClass('sts-dn').html("");
    }
    
 });
 
 jQuery("#txtYourAddr").focusout(function() {
    var to_addr = jQuery("#txtYourAddr").val();
    var check = true;  
    if(to_addr !=''){
       add_arr = to_addr.split(";");
       jQuery.each(add_arr,function(i,val){
           if(!IsEmail(val)){
               check = false;
               return false;
           }
       });
       
       if(!check){
         jQuery("#erroCheckMsg").removeClass('sts-dn').html("To address is not valid,please check");
         jQuery("#txtYourAddr").focus();         
       }
       
    }
   
 });
     
 
 
 jQuery("#sharebyEmailButton").live('click',function(){
    var to_addr = jQuery("#txtYourAddr").val();
    if(to_addr ==''){
       jQuery("#erroCheckMsg").removeClass('sts-dn').html("To address can't be empty");
       jQuery("#txtYourAddr").focus();
       return false;
    }
    var from_addr = jQuery("#txtFromAddr").val();
    var share_message = jQuery("#shareMessage").val();
    var share_detail = jQuery("#articleDetails").html();
    var title = jQuery("#headline").html();
    
    jQuery.post(ECOMM_AJAX.ajaxurl, {
          action: 'share_email_send',
          to:to_addr, 
          from:from_addr,
          subject:title,
          body_main: share_message,
          body_attach: share_detail
     },

     function(message, status) {
          if (status == 'success') {
               var results = eval('(' + message + ')'); //become object
               if (results.status != 'error'){
                   jQuery('#doneScreen').removeClass('sts-dn');
                   jQuery('#sharebyEmailButton').html('Share Again');
               } else{
                   jQuery('#errorScreen').removeClass('sts-dn');  
               }
          } else { //error case
              jQuery('#errorScreen').removeClass('sts-dn');            
          }
     });

 });
 
 /*
 jQuery("#cancelLink").live('click',function(){
    overlay_api.close();    
 });
*/ 
jQuery(".email").live('click',function(){
   var email_body_content = '<h3 id="headline">'+jQuery(this).attr('title')+'</h3>\n';
   email_body_content +='<a href="'+jQuery(location).attr('href')+'"  target="_blank">'+jQuery(location).attr('href')+'</a>\n';
   desc =  jQuery(this).parent('.social-media').prev('div.product_desc:first').find('span.description').html();
   email_body_content +='<div id="snippet">'+desc+'</div>\n';
   jQuery("#email_body").html(email_body_content);
   
   jQuery("a.imgLink img#thumbnail").attr("src", jQuery(this).attr('value'));
   
 });
 

//end for email share
  
  

});