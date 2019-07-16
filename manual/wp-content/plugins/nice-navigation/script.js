jQuery(document).ready(function(){


	jQuery('#nice_navigation-2 ul li').has('#nice_navigation-2 ul.children').prepend('<span>+</span>');
	jQuery('#nice_navigation-2 ul ul').hide();
	
    jQuery('#nice_navigation-2 span').click(
    function(){
    
	   
    }).toggle(
       function(){
       	   jQuery(this).text('-');
       	   
       	   var parent = jQuery(this).parent();
       	   jQuery(parent).find('>ul').slideToggle();
       },
       function(){
       	   jQuery(this).text('+');
       	   var parent = jQuery(this).parent();
       	   jQuery(parent).find('>ul').slideToggle();
       }
    );
    
    jQuery("li:contains('+')").css("background-image", "none")


});