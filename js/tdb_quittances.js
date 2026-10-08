function masquer_photo() {
  jQuery("#form_profile_picture").show();
  jQuery("#old-photo").hide();
}

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

function showInfos(sens) {
  if (sens) {
    jQuery("#phrase_pour_afficher").hide();
    jQuery("#masquer_infos").show();
    jQuery("#infos_proprio").show();
    jQuery("#editer_infos").show();
  } else {
    jQuery("#phrase_pour_afficher").show();
    jQuery("#masquer_infos").hide();
    jQuery("#infos_proprio").hide();
    jQuery("#editer_infos").hide();
    jQuery(".editable").attr("disabled", true);
    jQuery("#edit_proprio_annuler").hide();
    jQuery("#edit_save").hide();
    jQuery("#edit_proprio").show();
  }
}

(function ($) {
  jQuery(document).ready(function () {
    jQuery("#edit_photo_annuler").click(function () {
      jQuery("#form_profile_picture").hide();
      jQuery("#old-photo").show();
    });

    jQuery("#select_locataire").change(function () {
      console.log(jQuery(this).val());
      let id = jQuery(this).val();
      jQuery("#loyer_nu").val(jQuery("#loyer_" + id).val());
      jQuery("#charges").val(jQuery("#charges_" + id).val());
    });

    jQuery("#date_from").change(function () {
      jQuery("#date_to").val(jQuery("#date_from").val());
    });

    jQuery("#edit_proprio").click(function () {
      jQuery(".editable").attr("disabled", false);
      jQuery("#edit_proprio").hide();
      jQuery("#edit_proprio_annuler").show();
      jQuery("#edit_save").show();
    });

    jQuery("#edit_proprio_annuler").click(function () {
      jQuery(".editable").attr("disabled", true);
      jQuery("#edit_proprio").show();
      jQuery("#edit_proprio_annuler").hide();
      jQuery("#edit_save").hide();
    });
  });
})($);
