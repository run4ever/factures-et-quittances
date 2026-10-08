function showMenu(sens) {
  if (sens) {
    jQuery("#show_menu").hide();
    jQuery("#hide_menu").show();
    jQuery("#phone_menu").show();
  } else {
    jQuery("#show_menu").show();
    jQuery("#hide_menu").hide();
    jQuery("#phone_menu").hide();
  }
}

(function ($) {
  jQuery(document).ready(function () {

    jQuery('#menu-1').hover(function(){
      jQuery('#sous-menu-1').show();
      }, function(){
        jQuery('#sous-menu-1').hide();
      });

    jQuery('#sous-menu-1').hover(function(){
      jQuery('#sous-menu-1').show();
      }, function(){
        jQuery('#sous-menu-1').hide();
      });

      jQuery('#menu-2').hover(function(){
        jQuery('#sous-menu-2').show();
        }, function(){
          jQuery('#sous-menu-2').hide();
        });
  
      jQuery('#sous-menu-2').hover(function(){
        jQuery('#sous-menu-2').show();
        }, function(){
          jQuery('#sous-menu-2').hide();
        });
  });
})($);
