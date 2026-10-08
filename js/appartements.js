function showEditionRow(id, sens) {
  if (sens) {
    jQuery("#edition_" + id).show();
    jQuery("#croix_" + id).show();
    jQuery("#crayon_" + id).hide();
  } else {
    jQuery("#edition_" + id).hide();
    jQuery("#croix_" + id).hide();
    jQuery("#crayon_" + id).show();
  }
}

(function ($) {
  jQuery(document).ready(function () {});
})($);
