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
    jQuery("#infos_psy").show();
    jQuery("#editer_infos").show();
  } else {
    jQuery("#phrase_pour_afficher").show();
    jQuery("#masquer_infos").hide();
    jQuery("#infos_psy").hide();
    jQuery("#editer_infos").hide();
    jQuery(".editable").attr("disabled", true);
    jQuery("#edit_proprio_annuler").hide();
    jQuery("#edit_save").hide();
    jQuery("#edit_proprio").show();
  }
}

(function ($) {
  jQuery(document).ready(function () {

    jQuery("#nb_seances").change(function () {
      const today = new Date();
      const yyyy = today.getFullYear();
      let mm = today.getMonth() + 1; // Months start at 0!
      let dd = today.getDate();
      if (dd < 10) dd = '0' + dd;
      if (mm < 10) mm = '0' + mm;
      const formattedToday = yyyy + '-' + mm + '-' + dd;

      let nb = parseInt(jQuery(this).val());
      if(nb<1){
        nb=1;
        jQuery(this).val(1);
      }
      jQuery("#total").val(nb*parseInt(jQuery("#tarif").val()));
      jQuery("#seances").html("");
      let contenu = "";
      for (let i = 1; i < nb+1; i++) {
        contenu += "<div class='col-12 col-md-2'>";
        contenu += "<label for='date_seance_"+i+"' class='myFormLabels'>Date séance "+i+"</label>";
        contenu += "<input name='date_seance_"+i+"' id='date_seance_"+i+"' class='form-control editable-contrat' type='date' value='"+formattedToday+"'><span name='startDateSelected_"+i+"' id='startDateSelected_"+i+"'></span>";
        contenu += "</div>";
      }
      jQuery("#seances").html(contenu);
      
    });

    jQuery("#edit_photo_annuler").click(function () {
      jQuery("#form_profile_picture").hide();
      jQuery("#old-photo").show();
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
