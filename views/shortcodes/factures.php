<?php

function factures_func( ) {

require(dirname(__FILE__) . '/../../../../../wp-load.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$cli = 0;
if(is_user_logged_in()){

    global $wpdb;
    require(dirname(__FILE__) . '/../../tools/enums.php');
    require(dirname(__FILE__) . '/../../tools/mymenu.php');
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', '','',true );
    wp_enqueue_script( 'factures', plugins_url($pluginName) . '/js/factures.js?1021', '','',true );
    wp_enqueue_style('font-awesome-min_css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(),false, 'all');



    $script_upd_facture = plugins_url($pluginName) . "/scripts/update_facture.php";
    $script_upd_entreprise = plugins_url($pluginName) . "/scripts/update_entreprise.php";

    $user = wp_get_current_user();

    $html = "<div class='wrap'>";
    $html .= display_menu();
    $urlF = "/wp-content/plugins/quittances/scripts/factpdf.php?facture=";

    if(isset($_GET['newf'])){
        $facture_owner = $wpdb->get_var($wpdb->prepare("SELECT t1.id_sa FROM facqui_qtnc_clients as t1 LEFT JOIN facqui_qtnc_factures as t2 on t2.id_client = t1.id WHERE t2.uuid = %s", $_GET['newf']));
        if($facture_owner == $user->id){
            $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">";
            $html .= "<i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i><span style=\"margin-left:5px;font-size:16px;\">La facture a bien été enregistrée. <a href='$urlF" . $_GET['newf'] . "&way=1' class=\"alert-link\" target=\"_blank\">Cliquez ici pour la visualiser <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a> <br />Vous la retrouverez dans la liste des factures, en bas de page. Cliquez sur <a href='$urlF" . $_GET['newf'] . "&way=2' target=\"_blank\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i></a> pour la télécharger ou sur <a href='$urlF" . $_GET['newf'] . "&way=3'><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a> pour l'envoyer au patient.</span>";
            $html .= "</div>";
        }else{
            $urlRedirection = get_home_url() . "/factures";
            $wpdb->close();
            Header("Location: $urlRedirection");
        }
    }

    if(isset($_GET['reff']) && isset($_GET['melok']) && is_numeric($_GET['melok'])){
        $facture_owner = $wpdb->get_var($wpdb->prepare("SELECT t1.id_sa FROM facqui_qtnc_clients as t1 LEFT JOIN facqui_qtnc_factures as t2 on t2.id_client = t1.id WHERE t2.uuid = %s", $_GET['reff']));
        if($facture_owner == $user->id){
            switch($_GET['melok']){
                case 0:
                    $html .= "<div class=\"col-12 col-md-8 alert alert-danger\" role=\"alert\">";
                    $html .= "<i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i><span style=\"margin-left:5px;font-size:16px;\">Une erreur est survenue lors de l'envoi de la facture. Veuillez ré-essayer.</span>";
                    $html .= "</div>";
                    break;
                case 1:
                    $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">";
                    $html .= "<i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i><span style=\"margin-left:5px;font-size:16px;\">La facture a bien été envoyée au patient. <a href='$urlF" . $_GET['reff'] . "&way=1' class=\"alert-link\" target=\"_blank\">Cliquez ici pour la visualiser <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a></span>";
                    $html .= "</div>";
                    break;
            }
        }else{
            $urlRedirection = get_home_url() . "/factures";
            $wpdb->close();
            Header("Location: $urlRedirection");
        }
    }

    if(isset($_GET['cli']) && is_numeric($_GET['cli'])){
        $cli = $_GET['cli'];
        $client_owner = $wpdb->get_var($wpdb->prepare("SELECT id_sa FROM facqui_qtnc_clients WHERE id = %d", $cli));
        if($client_owner == $user->id){
            $html .= "<div class=\"col-12 col-md-8 alert alert-success\" role=\"alert\">";
            $html .= "<i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i><span style=\"margin-left:5px;font-size:16px;\">Patient enregistré. Vous pouvez générer une facture pour son compte ci-dessous.";
            $html .= "</div>";
        }else{
            $urlRedirection = get_home_url() . "/factures";
            $wpdb->close();
            Header("Location: $urlRedirection");
        }
    }

    $html .= "<h2>Factures</h2>";

    $patients = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "qtnc_clients WHERE id_sa = $user->id AND date_to is NULL");
    $entreprise = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "qtnc_entreprises WHERE user_id = $user->id");  

    $html .= "<div class=\"row tableTitle mt30\">";
        $html .= "<div class=\"col-10\">";
        $html .= "Infos psychologue";
        $html .= "</div>";
        $html .= "<div class=\"col-1\" id=\"editer_infos\" style=\"display:none;\">";
              $html .= "<img id=\"edit_proprio\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"25\">";
              $html .= "<img id=\"edit_proprio_annuler\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"25\">";
        $html .= "</div>";
        $html .= "<div id=\"masquer_infos\" class=\"col-1\" style=\"display:none;\" onclick=\"showInfos(false)\">";
              $html .= "<img id=\"see_info_annuler\" src=\"" . plugins_url($pluginName) . "/images/masquer.png\" class=\"clicable\" width=\"30\">";
        $html .= "</div>";
    $html .= "</div>";

    $html .= "<div id=\"infos_psy\" class=\"row mb10\" style=\"display:none;\">";
        $html .= "<div class=\"col-12 col-md-8\">";
            $html .= "<form id=\"form_copro\" method=POST action=$script_upd_entreprise accept-charset=\"UTF-8\">";
            $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
    
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"entete\" class=\"myFormLabels\">Entête facture</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"entete\" ";
                if(isset($entreprise->nom_entete)){$html .= "value=\"$entreprise->nom_entete\" ";}
                $html .= "required disabled>";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"nom_court\" class=\"myFormLabels\">Signature</label>";
                $html .= "<input type=\"text\" class=\"form-control editable\" name=\"nom_court\" ";
                if(isset($entreprise->nom_signature)){$html .= "value=\"$entreprise->nom_signature\" ";}
                $html .= "required disabled>";
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"adresse\" class=\"myFormLabels\">Adresse</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" name=\"adresse\" ";
                    if(isset($entreprise->adresse)){$html .= "value=\"$entreprise->adresse\" ";}
                    $html .= "required disabled>";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"signature\" class=\"myFormLabels\">Signature mail envoyé</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" name=\"signature\" ";
                    if(isset($entreprise->signature_email)){$html .= "value=\"$entreprise->signature_email\" ";}
                    $html .= "required disabled>";
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email\" ";
                    if(isset($entreprise->email)){$html .= "value=\"$entreprise->email\" ";}
                    $html .= "required disabled>";
                    $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<label for=\"email_nom\" class=\"myFormLabels\">Nom associé à l'e-mail</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" name=\"email_nom\" ";
                    if(isset($entreprise->email_nom)){$html .= "value=\"$entreprise->email_nom\" ";}
                    $html .= "required disabled>";
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-6\">";
                    $html .= "<label for=\"tarif\" class=\"myFormLabels\">Tarif</label>";
                    $html .= "<input type=\"number\" class=\"form-control editable\" id=\"tarif\" name=\"tarif\" ";
                    if(isset($entreprise->tarif)){$html .= "value=\"$entreprise->tarif\" ";}
                    $html .= "required disabled>";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-6\">";
                    $html .= "<label for=\"nb_seances_defaut\" class=\"myFormLabels\">Nb de séances facturées en général</label>";
                    $html .= "<input type=\"number\" class=\"form-control editable\" id=\"nb_seances_defaut\" name=\"nb_seances_defaut\" ";
                    if(isset($entreprise->nb_seances_defaut)){$html .= "value=\"$entreprise->nb_seances_defaut\" ";}
                    $html .= "required disabled>";
                $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-6\">";
                    $html .= "<label for=\"adeli\" class=\"myFormLabels\">Numéro ADELI</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" id=\"adeli\" name=\"adeli\" ";
                    if(isset($entreprise->adeli)){$html .= "value=\"" . esc_attr($entreprise->adeli) . "\" ";}
                    $html .= "disabled>";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-6\">";
                    $html .= "<label for=\"siret\" class=\"myFormLabels\">Siret</label>";
                    $html .= "<input type=\"text\" class=\"form-control editable\" id=\"siret\" name=\"siret\" ";
                    if(isset($entreprise->siret)){$html .= "value=\"" . esc_attr($entreprise->siret) . "\" ";}
                    $html .= "disabled>";
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
            $html .= "<label class=\"myFormLabels\">Signature de la facture</label>";
            $html .= "<div id=\"old-photo\" class=\"col-12\">";
                    $img_name = (!isset($entreprise->signature_pdf) || $entreprise->signature_pdf == null) ? "default.png" : $entreprise->signature_pdf;
                    $html .= "<img";
                    $html .= " class=\"clicable\" onclick=\"masquer_photo()\"";
                    $html .= " id=\"edit_photo\" src=\"".get_home_url()."/wp-content/uploads/factures/images/" . $img_name ."\" height=\"180\" >";
                $html .= "</div>";
                $html .= "<div id=\"new-photo\" class=\"col-12\">";
                    $html .= "<form id=\"form_profile_picture\" method=POST action='$script_upd_entreprise' style=\"display:none;\" class=\"mt-3\" accept-charset=\"UTF-8\" enctype=\"multipart/form-data\">";
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


    $html .= "<div class=\"row tableTitle mt30\" style=\"margin-top: 20px;\">";
        $html .= "<div class=\"col-11\">";
                $html .= "Ajouter une facture";
        $html .= "</div>";
        $html .= "<div class=\"col-1\">";
        $html .= "</div>";
    $html .= "</div>";

    if(count($patients) > 0){
    
        $html .= "<div id=\"ajout\" style=\"padding-top:10px;\">";
    
            $html .= "<form method=POST action=$script_upd_facture accept-charset=\"UTF-8\">";
    
            $html .= "<input name=\"action\" type=\"hidden\" value=\"1\">";
    
            $html .= "<div class=\"row mb10\">";
                    $html .= "<div class=\"col-12 col-md-2\">";
                        $html .= "<label for=\"patient\" class=\"myFormLabels\">Patient</label>";
                        $html .= "<select name=\"patient\" id=\"select_patient\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                        $html .= "<option value=\"\">Choisir un patient...</option>";
                                foreach ($patients as $patient) {
                                    $html .= "<option value=\"$patient->id\"";
                                    if($patient->id == $cli){$html .= " selected";}
                                    $html .= ">$patient->client</option>";
                                }
                        $html .= "</select>";
                    $html .= "</div>";

                    $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"date_facture\" class=\"myFormLabels\">Date de facture</label>
                        <input name=\"date_facture\" id=\"date_facture\" class=\"form-control editable-contrat\" type=\"date\" value=\"".date('Y-m-d')."\">
                        <span name=\"billDateSelected\" id=\"bilDateSelected\"></span>";
                    $html .= "</div>";
    
                    $html .= "<div class=\"col-12 col-md-2\">";
                        $html .= "<label for=\"nb_seances\" class=\"myFormLabels\">Nombre de séances</label>";
                        $html .= "<input id=\"nb_seances\" type=\"number\" class=\"form-control\" name=\"nb_seances\" value=\"$entreprise->nb_seances_defaut\" required>";
                    $html .= "</div>";

                    $totalDefault = $entreprise->nb_seances_defaut * $entreprise->tarif;
                    $html .= "<div class=\"col-12 col-md-2\">";
                        $html .= "<label for=\"total\" class=\"myFormLabels\">Montant total facturé</label>";
                        $html .= "<input id=\"total\" type=\"number\" class=\"form-control\" name=\"total\" value=\"$totalDefault\" required>";
                    $html .= "</div>";
    
            $html .= "</div>";

            $html .= "<div id=\"seances\" class=\"row mb10\">";
                for ($i=1; $i < $entreprise->nb_seances_defaut+1; $i++) { 
                    $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"date_seance_$i\" class=\"myFormLabels\">Date séance $i</label>
                        <input name=\"date_seance_$i\" id=\"date_seance_$i\" class=\"form-control editable-contrat\" type=\"date\" value=\"".date('Y-m-d')."\">
                        <span name=\"startDateSelected_$i\" id=\"startDateSelected_$i\"></span>";
                    $html .= "</div>";
                }
                $html .= "</div>";

            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-none col-md-10\">";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Enregistrer</button>";
                $html .= "</div>";
             $html .= "</div>";
            


            $html .= "</form>";
    
        $html .= "</div>";
    }else{
        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12\">";
                $html .= "Commencez par ajouter un patient sur <a href=\"/clients\">cette page</a>";
            $html .= "</div>";
        $html .= "</div>";
    }

    $html .= "<div class=\"row tableTitle mt30\" style=\"margin-top: 20px;\">";
        $html .= "<div class=\"col-11\">";
                $html .= "Factures";
        $html .= "</div>";
        $html .= "<div class=\"col-1\">";
        $html .= "</div>";
    $html .= "</div>";

    if(count($patients) > 0){

        foreach($patients as $patient){
        
            $html .= "<br />Patient : <span style=\"font-weight:bold;\">$patient->client</span>";
    
            $facturesEmises = $wpdb->get_results("select * from " . $wpdb->prefix . "qtnc_factures where id_client = " . $patient->id . " order by date_facture desc");
    
            if(count($facturesEmises)>0){
            foreach($facturesEmises as $facture){
                $url = "/wp-content/plugins/quittances/scripts/factpdf.php?facture=$facture->uuid";
                $html .= "<div class=\"row mt-2\">";
                    $html .= "<div class=\"col-12 col-md-3\">";
                        $html .= "<img id=\"crayon_$facture->id\" onclick=\"showEditionRow($facture->id, true)\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"20\">";
                        $html .= "<img id=\"croix_$facture->id\" onclick=\"showEditionRow($facture->id, false)\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"20\">";
                        $html .= "<span style=\"margin-left:5px;\">Facture datée du " . date('d/m/Y', strtotime($facture->date_facture)) ."</span>";
                    $html .= "</div>";
    
                    $html .= "<div class=\"col-12 col-md-1\">";
                        $html .= $facture->total . " €";
                    $html .= "</div>";

                    $html .= "<div class=\"col-12 col-md-6\">";
                        $seancesTab = explode(",",$facture->seances);
                        $html .= count($seancesTab) . " séances du " . str_replace(",", ", ", $facture->seances);
                    $html .= "</div>";
                    
                    $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "facture : <a href=\"$url&way=1\" target=\"_blank\"><i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a> - <a href=\"$url&way=2\" target=\"_blank\"><i class=\"fa fa-download\" aria-hidden=\"true\"></i></a> - <a href=\"$url&way=3\"><i class=\"fa fa-envelope\" aria-hidden=\"true\"></i></a>";
                    $html .= "</div>";
                $html .= "</div>";
    
                $html .= "<div id=\"edition_$facture->id\" class=\"row mt-2\" style=\"display:none;\">";
                    $html .= "<div class=\"col-8\">";
                        $html .= "<form method=POST action=$script_upd_facture accept-charset=\"UTF-8\">";
                            $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
                            $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$facture->uuid\">";
                            $html .= "<input type=\"hidden\" name=\"id\" value=\"$facture->id\">";
                                $html .= "<div class=\"row mt-2\">";
                                    $html .= "<div class=\"col-12 col-md-3\">";
                                    $html .= "<label for=\"date_facture\" class=\"myFormLabels\">Datée du...</label>
                                        <input name=\"date_facture\" id=\"date_facture\" class=\"form-control\" type=\"date\" value=\"$facture->date_facture\">
                                        <span name=\"startDateSelected\" id=\"startDateSelected\"></span>";
                                    $html .= "</div>";
                                
                                    $html .= "<div class=\"col-12 col-md-2\">";
                                        $html .= "<label for=\"total\" class=\"myFormLabels\">Total facturé</label>";
                                        $html .= "<input id=\"total\" type=\"number\" class=\"form-control\" name=\"total\" value=\"$facture->total\" required>";
                                    $html .= "</div>";
    
                                    $html .= "<div class=\"col-12 col-md-5\">";
                                        $html .= "<label for=\"seances\" class=\"myFormLabels\">Séances</label>";
                                        $html .= "<input id=\"seances\" type=\"text\" class=\"form-control\" name=\"seances\" value=\"$facture->seances\" required>";
                                    $html .= "</div>";
    
                                    $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                                        $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Modifier</button>";
                                    $html .= "</div>";
                                $html .= "</div>";
                        $html .= "</form>";
                    $html .= "</div>";
                    $html .= "<div class=\"col-1\">";
                        $html .= "<form method=POST action=$script_upd_facture accept-charset=\"UTF-8\">";
                            $html .= "<input type=\"hidden\" name=\"action\" value=\"-1\">";
                            $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$facture->uuid\">";
                            $html .= "<input type=\"hidden\" name=\"id\" value=\"$facture->id\">";
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
                        $html .= "Aucune facture enregistrée pour ce patient.";
                    $html .= "</div>";
                $html .= "</div>";
            }
        }}else{
            $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12\">";
                    $html .= "Aucune facture enregistrée.";
                $html .= "</div>";
            $html .= "</div>";
        }


    //fermeture du div wrap
    $html .= "</div>";
}
else{
    header("Location: " . wp_login_url(get_home_url() . "/factures"));
}

return $html;

}
add_shortcode( 'factures', 'factures_func' );

?>