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

// Bouton "Loyer reçu" : confirmation adaptée selon que la quittance est envoyée ou non
function confirmerLoyerRecu(form) {
  let envoi = form.querySelector("input[name=envoi]");
  let avecEnvoi = envoi && envoi.checked;
  let message = "Enregistrer le loyer de " + form.dataset.mois + " (" + form.dataset.montant + " €) pour " + form.dataset.locataire;
  message += avecEnvoi ? " et envoyer la quittance à " + form.dataset.email + " ?" : ", sans envoyer de quittance ?";
  if (!confirm(message)) {
    return false;
  }
  form.querySelector("button").disabled = true;
  return true;
}

// Mémorise, par locataire, le choix "avec envoi de mail" dans ce navigateur
jQuery(document).ready(function () {
  jQuery(".envoi-quittance").each(function () {
    let cle = "qtnc_envoi_" + this.dataset.locataire;
    try {
      let memo = localStorage.getItem(cle);
      if (memo !== null && !this.disabled) {
        this.checked = memo === "1";
      }
    } catch (e) {}
    jQuery(this).change(function () {
      try {
        localStorage.setItem(cle, this.checked ? "1" : "0");
      } catch (e) {}
    });
  });
});
