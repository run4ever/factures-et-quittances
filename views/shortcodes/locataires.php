<?php

function locataires_func( ) {

require(dirname(__FILE__) . '/../../../../../wp-load.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(is_user_logged_in()){

    global $wpdb;
    require(dirname(__FILE__) . '/../../tools/enums.php');
    require(dirname(__FILE__) . '/../../tools/mymenu.php');
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', '','',true );
    wp_enqueue_script( 'apparts', plugins_url($pluginName) . '/js/locataires.js?1000', '','',true );

    $script_upd_locataire = plugins_url($pluginName) . "/scripts/update_locataire.php";

    $user = wp_get_current_user();
    $appartements = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "qtnc_appartements WHERE id_proprio = $user->id AND date_vente is NULL");

    $html = "<div class='wrap'>";
    
    $html .= display_menu();

    $html .= "<h2>Locataires</h2>";
    $html .= "<br /><br />";

    $html .= "<div class=\"row tableTitle mt30\">";
        $html .= "<div class=\"col-11\">";
              $html .= "Ajouter un locataire";
        $html .= "</div>";
  $html .= "</div>";

    if(count($appartements) > 0){

        $html .= "<div id=\"ajout\" style=\"padding-top:10px;\">";

        $html .= "<form method=POST action=$script_upd_locataire accept-charset=\"UTF-8\">";

        $html .= "<input name=\"action\" type=\"hidden\" value=\"1\">";

        $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"appartement\" class=\"myFormLabels\">Appartement</label>";
                    $html .= "<select name=\"appartement\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                    $html .= "<option value=\"\">Choisir un appartement...</option>";
                            foreach ($appartements as $appart) {
                                $html .= "<option value=\"$appart->id\">$appart->adresse</option>";
                            }
                    $html .= "</select>";
                $html .= "</div>";
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"debut_bail\" class=\"myFormLabels\">Début du bail...</label>
                    <input name=\"debut_bail\" id=\"debut_bail\" class=\"form-control editable-contrat\" type=\"date\" value=\"\">
                    <span name=\"startDateSelected\" id=\"startDateSelected\"></span>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"nom\" class=\"myFormLabels\">Civ. Nom Prénom</label>";
                    $html .= "<input id=\"nom\" type=\"text\" class=\"form-control\" name=\"nom\" required>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-1\">";
                    $html .= "<label for=\"coloc\" class=\"myFormLabels\">Colocation?</label>";
                    $html .= "<select name=\"coloc\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                        $html .= "<option value=\"0\">Non</option>";
                        $html .= "<option value=\"1\">Oui</option>";                            
                    $html .= "</select>";
                $html .= "</div>";
            
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                    $html .= "<input id=\"email\" type=\"email\" class=\"form-control\" name=\"email\" required>";
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
                $html .= "Commencez par ajouter un appartement sur <a href=\"/wp-admin/admin.php?page=apparts\">cette page</a>";
                $html .= "</div>";

        $html .= "</div>";
    }

    $html .= "<br /><br />";

    $sql = "select t1.*, t2.adresse, t2.cp, t2.ville from " . $wpdb->prefix . "qtnc_locataires as t1 left join " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id where t1.id_appartement in(SELECT id FROM " . $wpdb->prefix . "qtnc_appartements as t3 WHERE t3.id_proprio = $user->id AND t3.date_vente is NULL) and (t1.date_to is NULL or t1.date_to >= CURDATE()) order by t1.date_from asc";
    $locataires = $wpdb->get_results($sql);

    foreach($locataires as $locataire){
        $html .= "<div class=\"row mt-4 mb-4\">";
            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<img id=\"crayon_$locataire->id\" onclick=\"showEditionRow($locataire->id, true)\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"20\">";
                $html .= "<img id=\"croix_$locataire->id\" onclick=\"showEditionRow($locataire->id, false)\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"20\">";
                $html .= "<span style=\"margin-left:5px;\">" . $locataire->locataire;
                if($locataire->is_coloc == 1){$html .= " (colocation)";}
                $html .= "</span>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<span style=\"margin-left:5px;\">$locataire->adresse , $locataire->cp $locataire->ville</span>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<span style=\"margin-left:5px;\">Depuis le " . date('d/m/Y', strtotime($locataire->date_from)) ."</span>";
                if(!empty($locataire->date_to)){
                    $html .= "<span style=\"margin-left:5px;\"> - fin de bail le " . date('d/m/Y', strtotime($locataire->date_to)) ."</span>";
                }
            $html .= "</div>";
        $html .= "</div>";

        $html .= "<div id=\"edition_$locataire->id\" class=\"mt-2\" style=\"display:none;\">";
            $html .= "<div class=\"col-12\">";
                $html .= "<form method=POST action=$script_upd_locataire accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$locataire->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$locataire->id\">";
                        $html .= "<div class=\"row mt-2\">";
                            $html .= "<div class=\"col-12 col-md-3\">";
                                $html .= "<label for=\"nom\" class=\"myFormLabels\">Civ. Nom Prénom</label>";
                                $html .= "<input id=\"nom\" type=\"text\" class=\"form-control\" name=\"nom\" value=\"$locataire->locataire\" required>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-3\">";
                                $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                                $html .= "<input id=\"email\" type=\"email\" class=\"form-control\" name=\"email\" value=\"$locataire->email\" required>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-2\">";
                                $html .= "<label for=\"debut_bail\" class=\"myFormLabels\">Depuis le...</label>
                                    <input name=\"debut_bail\" id=\"debut_bail\" class=\"form-control\" type=\"date\" value=\"$locataire->date_from\">
                                    <span name=\"startDateSelected_$locataire->id\" id=\"startDateSelected_$locataire->id\"></span>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-2\">";
                                $html .= "<label for=\"ville\" class=\"myFormLabels\">Appartement</label>";
                                $html .= "<select name=\"appartement\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                                   // $html .= "<option value=\"\">Choisir un appartement...</option>";
                                            foreach ($appartements as $appart) {
                                                $html .= "<option value=\"$appart->id\"";
                                                if($appart->id == $locataire->id_appartement){$html .= " selected";}
                                                $html .= ">$appart->adresse</option>";
                                            }
                                    $html .= "</select>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-1\">";
                                $html .= "<label for=\"coloc\" class=\"myFormLabels\">Colocation?</label>";
                                $html .= "<select name=\"coloc\" class=\"form-select\" style=\"min-height: 33px;\" required>";
                        $html .= "<option value=\"0\"";
                        if($locataire->is_coloc != 1){$html .= " selected";}
                        $html .= ">Non</option>";
                        $html .= "<option value=\"1\"";
                        if($locataire->is_coloc == 1){$html .= " selected";}
                        $html .= ">Oui</option>";                            
                    $html .= "</select>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-1\" style=\"text-align:right;\">";
                                $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Modifier</button>";
                            $html .= "</div>";
                        $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";
            $html .= "<div class=\"row\">";
            $html .= "<div class=\"col-12\">";
                $html .= "<form method=POST action=$script_upd_locataire accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"-1\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$locataire->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$locataire->id\">";
                    $html .= "<div class=\"row mt-2\">";
                        $html .= "<div class=\"col-12 col-md-3\">";
                            $html .= "<label for=\"date_depart\" class=\"myFormLabels\">Fin de bail le...</label>
                                <input name=\"date_depart\" id=\"date_depart\" class=\"form-control\" type=\"date\" value=\"$locataire->date_to\" required>
                                <span name=\"endDateSelected_$locataire->id\" id=\"endDateSelected_$locataire->id\"></span>";
                            $html .= "</div>";

                        $html .= "<div class=\"col-12 col-md-3\">";
                            $html .= "<button type=\"submit\" class=\"btn btn-danger\" style=\"margin-top: 25px; width:100%;\">Enregistrer la fin de bail</button>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";
            $html .= "</div>";

        $html .= "</div>";

    }


    //fermeture du div wrap
    $html .= "</div>";
}
else{
    header("Location: " . wp_login_url(get_home_url() . "/locataires"));
}

return $html;

}
add_shortcode( 'locataires', 'locataires_func' );

?>