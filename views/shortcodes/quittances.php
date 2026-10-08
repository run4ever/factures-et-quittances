<?php

function quittances_func( ) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

if(is_user_logged_in()){

    global $wpdb;
    require(dirname(__FILE__) . '/../../tools/enums.php');
    require(dirname(__FILE__) . '/../../tools/mymenu.php');

   wp_enqueue_style('bootstrap-min_css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), false, 'all');
   wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', '','',true );
   wp_enqueue_script('tdb_quittances', plugins_url($pluginName) . '/js/tdb_quittances.js?1023', '','',true );
   wp_enqueue_style('font-awesome-min_css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(),false, 'all');

   $script_upd_loyer = plugins_url($pluginName) . "/scripts/update_loyer.php";
   $script_upd_proprio = plugins_url($pluginName) . "/scripts/update_proprio.php";
   $script_upd_locataire = plugins_url($pluginName) . "/scripts/update_locataire.php";

   $user = wp_get_current_user();
   $sql = "SELECT t1.*, t2.loyer, t2.charges FROM " . $wpdb->prefix . "qtnc_locataires as t1 left join " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id WHERE t2.id_proprio = " . $user->id . " ORDER BY t1.date_from DESC";

  $locataires = $wpdb->get_results($sql);
  $proprio = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "qtnc_proprietaires WHERE user_id = $user->id");  

    $html = "<div class='wrap'>";
    $html .= display_menu();
    $html .= "<h2>Gestion locative</h2>";

    if(isset($_GET['envoi']) && $_GET['envoi'] == 'ok'){
        $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">La quittance a bien été envoyée au locataire.</div>";
    }
    if(isset($_GET['lien']) && $_GET['lien'] == 'nouveau'){
        $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">Nouveau lien créé : l'ancien lien de l'espace locataire ne fonctionne plus. Le nouveau sera envoyé avec la prochaine quittance.</div>";
    }
    if(isset($_GET['envoi']) && $_GET['envoi'] == 'termine'){
        $html .= "<div class=\"col-12 col-md-8 alert alert-warning\" role=\"alert\">Le bail de ce locataire est terminé, rien n'a été ajouté ni envoyé.</div>";
    }
    if(isset($_GET['loyer']) && $_GET['loyer'] == 'ok'){
        $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">Le loyer a bien été enregistré (sans envoi de quittance).</div>";
    }
    if(isset($_GET['envoi']) && $_GET['envoi'] == 'deja'){
        $html .= "<div class=\"col-12 col-md-8 alert alert-warning\" role=\"alert\">Ce loyer avait déjà été enregistré, rien n'a été ajouté ni envoyé.</div>";
    }

    $html .= "<div class=\"row tableTitle mt30\">";
        $html .= "<div class=\"col-10\">";
              $html .= "Infos propriétaire";
        $html .= "</div>";
        $html .= "<div class=\"col-1\" id=\"editer_infos\" style=\"display:none;\">";
              $html .= "<img id=\"edit_proprio\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"25\">";
              $html .= "<img id=\"edit_proprio_annuler\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"25\">";
        $html .= "</div>";
        $html .= "<div id=\"masquer_infos\" class=\"col-1\" style=\"display:none;\" onclick=\"showInfos(false)\">";
              $html .= "<img id=\"see_info_annuler\" src=\"" . plugins_url($pluginName) . "/images/masquer.png\" class=\"clicable\" width=\"30\">";
        $html .= "</div>";
  $html .= "</div>";

  $html .= "<div id=\"infos_proprio\" class=\"row mb10\" style=\"display:none;\">";
  $html .= "<div class=\"col-12 col-md-8\">";
        $html .= "<form id=\"form_copro\" method=POST action=$script_upd_proprio accept-charset=\"UTF-8\">";
        $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
  
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"entete\" class=\"myFormLabels\">Entête quittance</label>";
            $html .= "<input type=\"text\" class=\"form-control editable\" name=\"entete\" ";
            if(isset($proprio->nom_entete)){$html .= "value=\"$proprio->nom_entete\" ";}
            $html .= "required disabled>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"nom_court\" class=\"myFormLabels\">Nom dans le paragraphe</label>";
            $html .= "<input type=\"text\" class=\"form-control editable\" name=\"nom_court\" ";
            if(isset($proprio->nom_contenu)){$html .= "value=\"$proprio->nom_contenu\" ";}
            $html .= "required disabled>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"adresse\" class=\"myFormLabels\">Adresse</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"adresse\" ";
                if(isset($proprio->adresse)){$html .= "value=\"$proprio->adresse\" ";}
                $html .= "required disabled>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"signature\" class=\"myFormLabels\">Signature mail envoyé</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"signature\" ";
                if(isset($proprio->signature_email)){$html .= "value=\"$proprio->signature_email\" ";}
                $html .= "required disabled>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email\" ";
                if(isset($proprio->email)){$html .= "value=\"$proprio->email\" ";}
                $html .= "required disabled>";
                $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-6\">";
            $html .= "<label for=\"email_nom\" class=\"myFormLabels\">Nom associé à l'e-mail</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email_nom\" ";
                if(isset($proprio->email_nom)){$html .= "value=\"$proprio->email_nom\" ";}
                $html .= "required disabled>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12\">";
            $html .= "<button id=\"edit_save\" type=\"submit\" class=\"btn btn-success lh11\" style=\"display:none; margin-top:25px; width:100%;\">Enregistrer</button>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "</form>";
    $html .= "</div>";
    $html .= "<div class=\"col-12 col-md-4\">";
        $html .= "<div class=\"row mb10\">";
        $html .= "<label class=\"myFormLabels\">Signature de la quittance</label>";
        $html .= "<div id=\"old-photo\" class=\"col-12\">";
                $img_name = (!isset($proprio->signature_pdf) || $proprio->signature_pdf == null) ? "default.png" : $proprio->signature_pdf;
                $html .= "<img";
                $html .= " class=\"clicable\" onclick=\"masquer_photo()\"";
                $html .= " id=\"edit_photo\" src=\"".get_home_url()."/wp-content/uploads/quittances/images/" . $img_name ."\" height=\"180\" >";
            $html .= "</div>";
            $html .= "<div id=\"new-photo\" class=\"col-12\">";
                $html .= "<form id=\"form_profile_picture\" method=POST action='$script_upd_proprio' style=\"display:none;\" class=\"mt-3\" accept-charset=\"UTF-8\" enctype=\"multipart/form-data\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"2\">";
                    $html .= "<input type='hidden' id='MAX_FILE_SIZE' name='MAX_FILE_SIZE' value='5242880' />";
                    $html .= "<div class=\"row\">";
                        $html .= "<div class=\"col-12 mb-3\">";
                                    $html .= "<label for=\"photofile\" style=\"font-weight:bold; margin-bottom:5px;\">Charger une nouvelle signature :</label>";
                                    $html .= "<input class=\"form-control\" type=\"file\" id=\"photofile\" name=\"photofile\" required>
                                </div>";
                    $html .= "</div>";
                    $html .= "<div class=\"row\">";
                        $html .= "<div class=\"col-12 md-3\" style=\"text-align:right;\">";
                                $html .= "<button type=\"button\" id=\"edit_photo_annuler\" class=\"btn btn-danger\" style=\"margin-right:20px;\">Annuler</button>";
                                $html .= "<button type=\"submit\" id=\"edit_save_img\" class=\"btn btn-success\">Enregistrer la nouvelle signature</button>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";
        $html .= "</div>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "<div id=\"phrase_pour_afficher\" onclick=\"showInfos(true)\" class=\"row mb10 clicable\">";
        $html .= "<div class=\"col-12 col-md-12\" style=\"text-align:center;\">";
            $html .= "Cliquer pour afficher";
        $html .= "</div>";
    $html .= "</div>";




        /*
        $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"entete\" class=\"myFormLabels\">Entête quittance</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"entete\" value=\"$proprio->nom_entete\" required disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"nom_court\" class=\"myFormLabels\">Nom dans le paragraphe</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"nom_court\" value=\"$proprio->nom_contenu\" required disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"adresse\" class=\"myFormLabels\">Adresse</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"adresse\" value=\"$proprio->adresse\" required disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-2\">";
        $html .= "<label for=\"signature_pdf\" class=\"myFormLabels\">Image signature PDF</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"signature_pdf\" value=\"$proprio->signature_pdf\" required disabled>";
        $html .= "</div>";

    $html .= "</div>";
    $html .= "<div class=\"row mb10\">";

    $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"email_nom\" class=\"myFormLabels\">Nom associé à l'e-mail</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email_nom\" value=\"$proprio->email_nom\" disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email\" value=\"$proprio->email\" disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-3\">";
        $html .= "<label for=\"signature\" class=\"myFormLabels\">Signature mail envoyé</label>";
        $html .= "<input type=\"text\" class=\"form-control editable\" name=\"signature\" value=\"$proprio->signature_email\" required disabled>";
        $html .= "</div>";

        $html .= "<div class=\"col-12 col-md-2 r-5\">";
        $html .= "<button id=\"edit_save\" type=\"submit\" class=\"btn btn-success lh11\" style=\"display:none; margin-top:25px; width:100%;\">Enregistrer</button>";
        $html .= "</div>";
*/
  //$html .= "</div>";

    $html .= "<div class=\"row tableTitle mt30\" style=\"margin-top: 20px;\">";
    $html .= "<div class=\"col-11\">";
          $html .= "Ajouter un loyer perçu";
    $html .= "</div>";
    $html .= "<div class=\"col-1\">";
    $html .= "</div>";
$html .= "</div>";


if(count($locataires) > 0){
    foreach ($locataires as $locataire) {
        $html .= "<div class=\"row\" style=\"display:none;\">";
        $html .= "<div class=\"col-3\">";
            $html .= "<input id=\"loyer_$locataire->id\" type=\"hidden\" value=\"$locataire->loyer\">";
        $html .= "</div>";
        $html .= "<div class=\"col-3\">";
            $html .= "<input id=\"charges_$locataire->id\" type=\"hidden\" value=\"$locataire->charges\">";
        $html .= "</div>";
        $html .= "</div>";
    }

    $thisMonthFirstDay = date("Y-m") . "-01";
    $newtMonthFirstDay = date('Y-m-d', strtotime($thisMonthFirstDay. ' + 1 months'));
    $thisMonthLastDay = date('Y-m-d', strtotime($newtMonthFirstDay. ' - 1 days'));

    $html .= "<div id=\"ajout\" style=\"padding-top:10px;\">";

        $html .= "<form method=POST action=$script_upd_loyer accept-charset=\"UTF-8\">";

        $html .= "<input name=\"action\" type=\"hidden\" value=\"1\">";

        $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"locataire\" class=\"myFormLabels\">Locataire</label>";
                    $html .= "<select name=\"locataire\" id=\"select_locataire\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                    $html .= "<option value=\"\">Choisir un locataire...</option>";
                            foreach ($locataires as $locataire) {
                                $html .= "<option value=\"$locataire->id\">$locataire->locataire";
                                if(!empty($locataire->date_to) && $locataire->date_to < date('Y-m-d')){$html .= " (bail terminé)";}
                                $html .= "</option>";
                            }
                    $html .= "</select>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-2\">";
                $html .= "<label for=\"date_from\" class=\"myFormLabels\">Période du...</label>
                    <input name=\"date_from\" id=\"date_from\" class=\"form-control editable-contrat\" type=\"date\" value=\"$thisMonthFirstDay\">
                    <span name=\"startDateSelected\" id=\"startDateSelected\"></span>";
                $html .= "</div>";
            
                $html .= "<div class=\"col-12 col-md-2\">";
                $html .= "<label for=\"date_to\" class=\"myFormLabels\">Au...</label>";
                $html .= "<input name=\"date_to\" id=\"date_to\" class=\"form-control editable-contrat\" type=\"date\" value=\"$thisMonthLastDay\">
                    <span name=\"endDateSelected\" id=\"endDateSelected\"></span>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"loyer_nu\" class=\"myFormLabels\">Montant du loyer nu</label>";
                    $html .= "<input id=\"loyer_nu\" type=\"number\" class=\"form-control\" name=\"loyer_nu\" required>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"charges\" class=\"myFormLabels\">Charges</label>";
                    $html .= "<input id=\"charges\" type=\"number\" class=\"form-control\" name=\"charges\" required>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                    $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Ajouter</button>";
                $html .= "</div>";

        $html .= "</div>";
        $html .= "</form>";

    $html .= "</div>";
}else{
    $html .= "<div class=\"row mb10\">";
        $html .= "<div class=\"col-12\">";
    $apparts = $wpdb->get_results("select * from " . $wpdb->prefix . "qtnc_appartements where id_proprio = $user->id and date_vente is NULL order by date_achat desc");
    if(count($apparts)>0){
            $html .= "Commencez par ajouter un locataire sur <a href=\"/wp-admin/admin.php?page=locataires\">cette page</a>";
    }else{    
            $html .= "Commencez par ajouter un appartement sur <a href=\"/wp-admin/admin.php?page=apparts\">cette page</a>";
    }
        $html .= "</div>";
    $html .= "</div>";
}


    $html .= "<div class=\"row tableTitle mt30\" style=\"margin-top: 20px;\">";
    $html .= "<div class=\"col-11\">";
          $html .= "Loyers perçus";
    $html .= "</div>";
    $html .= "<div class=\"col-1\">";
    $html .= "</div>";
$html .= "</div>";


if(count($locataires) > 0){

    foreach($locataires as $locataire){

        $adresse = $wpdb->get_var("select adresse from " . $wpdb->prefix . "qtnc_appartements where id = ".$locataire->id_appartement);

        $html .= "<br /><strong>$locataire->locataire, $adresse</strong>";
        if(!empty($locataire->date_to)){
            $html .= " <span style=\"font-size:14px; color:#666;\">(fin de bail le " . date('d/m/Y', strtotime($locataire->date_to)) . ")</span>";
        }

        // Bouton "Loyer reçu" : enregistre le prochain loyer, avec ou sans envoi de la quittance
        $periode = qtnc_prochaine_periode($locataire->id);
        $mois = qtnc_mois_annee_fr($periode['from']);
        $montant = $locataire->loyer + $locataire->charges;
        if($periode['actif']){
            $html .= "<form method=POST action=$script_upd_loyer accept-charset=\"UTF-8\" style=\"display:inline; margin-left:15px;\" onsubmit=\"return confirmerLoyerRecu(this);\""
                . " data-mois=\"" . esc_attr($mois) . "\" data-montant=\"" . esc_attr($montant) . "\" data-locataire=\"" . esc_attr($locataire->locataire) . "\" data-email=\"" . esc_attr($locataire->email) . "\">";
                $html .= "<input type=\"hidden\" name=\"action\" value=\"2\">";
                $html .= "<input type=\"hidden\" name=\"locataire\" value=\"$locataire->id\">";
                $html .= "<input type=\"hidden\" name=\"period_from\" value=\"" . $periode['from'] . "\">";
                $html .= "<button type=\"submit\" class=\"btn btn-sm btn-success\"><i class=\"fa fa-check\" aria-hidden=\"true\"></i> Loyer reçu : $mois ($montant €)</button>";
                $html .= "<label style=\"margin-left:8px; font-size:14px;\"";
                if(empty($locataire->email)){$html .= " title=\"Aucun e-mail renseigné pour ce locataire\"";}
                $html .= "><input type=\"checkbox\" name=\"envoi\" value=\"1\" class=\"envoi-quittance\" data-locataire=\"$locataire->id\"";
                $html .= empty($locataire->email) ? " disabled" : " checked";
                $html .= "> avec envoi de mail</label>";
            $html .= "</form>";
        }

        // Espace locataire : lien secret et renouvellement du lien
        $url_espace = qtnc_url_espace_locataire(qtnc_token_locataire($locataire->id));
        $html .= "<span style=\"margin-left:15px; font-size:14px;\">";
            $html .= "<a href=\"" . esc_url($url_espace) . "\" target=\"_blank\" rel=\"noopener\" title=\"Le lien envoyé au locataire en bas de chaque quittance\"><i class=\"fa fa-user\" aria-hidden=\"true\"></i> Espace locataire</a>";
            $html .= "<form method=POST action=$script_upd_locataire accept-charset=\"UTF-8\" style=\"display:inline; margin-left:10px;\" onsubmit=\"return confirm('" . esc_js("Créer un nouveau lien pour $locataire->locataire ? L'ancien lien ne fonctionnera plus.") . "');\">";
                $html .= "<input type=\"hidden\" name=\"action\" value=\"3\">";
                $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$locataire->uuid\">";
                $html .= "<input type=\"hidden\" name=\"id\" value=\"$locataire->id\">";
                $html .= "<button type=\"submit\" class=\"btn btn-link btn-sm p-0\" style=\"font-size:14px;\" title=\"Coupe l'accès par l'ancien lien\"><i class=\"fa fa-refresh\" aria-hidden=\"true\"></i> Nouveau lien</button>";
            $html .= "</form>";
        $html .= "</span>";

        $loyersPayes = $wpdb->get_results("select * from " . $wpdb->prefix . "qtnc_loyers where id_locataire = " . $locataire->id . " order by period_from desc");

        $html .= "<details class=\"mt-2\">";
        $html .= "<summary class=\"clicable\" style=\"font-size:14px;\">Quittances (" . count($loyersPayes) . ")";
        if(count($loyersPayes) > 0){
            $html .= " - dernière : " . qtnc_mois_annee_fr($loyersPayes[0]->period_from);
        }
        $html .= "</summary>";

        if(count($loyersPayes)>0){
        foreach($loyersPayes as $loyer){
            $url = "/wp-content/plugins/quittances/scripts/quitpdf.php?locataire=$locataire->uuid&loyer=$loyer->uuid";
            $html .= "<div class=\"row mt-2\">";
                $html .= "<div class=\"col-12 col-md-3\">";
                    $html .= "<img id=\"crayon_$loyer->id\" onclick=\"showEditionRow($loyer->id, true)\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"20\">";
                    $html .= "<img id=\"croix_$loyer->id\" onclick=\"showEditionRow($loyer->id, false)\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"20\">";
                    $html .= "<span style=\"margin-left:5px;\">Du " . date('d/m/Y', strtotime($loyer->period_from)) . " au " . date('d/m/Y', strtotime($loyer->period_to)) ."</span>";
                $html .= "</div>";

                $total = $loyer->loyer_nu + $loyer->charges;
                $html .= "<div class=\"col-12 col-md-3\">";
                    $html .= $total . " € <span style=\"font-size:12px;\">(loyer de " . $loyer->loyer_nu . " € + " . $loyer->charges . " € de charges)</span>";
                $html .= "</div>";
                
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "quittance : <a href=\"$url&way=1\" target=\"_blank\"><i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a> - <a href=\"$url&way=2\" target=\"_blank\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i></a> - <a href=\"$url&way=3\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a>";
                $html .= "</div>";
            $html .= "</div>";

            $html .= "<div id=\"edition_$loyer->id\" class=\"row mt-2\" style=\"display:none;\">";
                $html .= "<div class=\"col-8\">";
                    $html .= "<form method=POST action=$script_upd_loyer accept-charset=\"UTF-8\">";
                        $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
                        $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$loyer->uuid\">";
                        $html .= "<input type=\"hidden\" name=\"id\" value=\"$loyer->id\">";
                            $html .= "<div class=\"row mt-2\">";
                                $html .= "<div class=\"col-12 col-md-3\">";
                                $html .= "<label for=\"date_from\" class=\"myFormLabels\">Période du...</label>
                                    <input name=\"date_from\" id=\"date_from\" class=\"form-control\" type=\"date\" value=\"$loyer->period_from\">
                                    <span name=\"startDateSelected\" id=\"startDateSelected\"></span>";
                                $html .= "</div>";
                            
                                $html .= "<div class=\"col-12 col-md-3\">";
                                $html .= "<label for=\"date_to\" class=\"myFormLabels\">Au...</label>";
                                $html .= "<input name=\"date_to\" id=\"date_to\" class=\"form-control\" type=\"date\" value=\"$loyer->period_to\">
                                    <span name=\"endDateSelected\" id=\"endDateSelected\"></span>";
                                $html .= "</div>";

                                $html .= "<div class=\"col-12 col-md-2\">";
                                    $html .= "<label for=\"loyer_nu\" class=\"myFormLabels\">Loyer</label>";
                                    $html .= "<input id=\"loyer_nu\" type=\"number\" class=\"form-control\" name=\"loyer_nu\" value=\"$loyer->loyer_nu\" required>";
                                $html .= "</div>";

                                $html .= "<div class=\"col-12 col-md-2\">";
                                    $html .= "<label for=\"charges\" class=\"myFormLabels\">Charges</label>";
                                    $html .= "<input id=\"charges\" type=\"number\" class=\"form-control\" name=\"charges\" value=\"$loyer->charges\" required>";
                                $html .= "</div>";

                                $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                                    $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Modifier</button>";
                                $html .= "</div>";
                            $html .= "</div>";
                    $html .= "</form>";
                $html .= "</div>";
                $html .= "<div class=\"col-1\">";
                    $html .= "<form method=POST action=$script_upd_loyer accept-charset=\"UTF-8\">";
                        $html .= "<input type=\"hidden\" name=\"action\" value=\"-1\">";
                        $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$loyer->uuid\">";
                        $html .= "<input type=\"hidden\" name=\"id\" value=\"$loyer->id\">";
                        $html .= "<div class=\"row mt-2\">";
                            $html .= "<div class=\"col-12\">";
                                $html .= "<button type=\"submit\" class=\"btn btn-danger\" style=\"margin-top: 25px; width:100%;\">Supprimer</button>";
                            $html .= "</div>";
                        $html .= "</div>";
                    $html .= "</form>";
                $html .= "</div>";
            $html .= "</div>";
        }}else{
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12\">";
                    $html .= "Aucune saisie effectuée pour ce locataire.";
                $html .= "</div>";
            $html .= "</div>";
        }
        $html .= "</details>";
    }}else{
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12\">";
                $html .= "Aucune saisie effectuée.";
            $html .= "</div>";
        $html .= "</div>";
    }




    //fin div class wrap
    $html .= "</div>";
}
else{
    header("Location: " . wp_login_url(get_home_url() . "/quittances"));
}
    return $html;
}
add_shortcode( 'quittances', 'quittances_func' );

?>