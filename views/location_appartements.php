<?php

require(dirname(__FILE__) . '/../../../../wp-load.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(is_user_logged_in()){

    global $wpdb;
    require(dirname(__FILE__) . '/../tools/enums.php');

    wp_enqueue_script( 'apparts', plugins_url($pluginName) . '/js/appartements.js?1000', '','',true );

    $script_upd_appart = plugins_url($pluginName) . "/scripts/update_appart.php";

    $user = wp_get_current_user();
    $html = "<div class='wrap'>";
    $html .= "<h2>Appartements</h2>";

    $html .= "<br /><br />";

    $html .= "<div class=\"row tableTitle mt30\">";
    $html .= "<div class=\"col-11\">";
          $html .= "Ajouter un appartement";
    $html .= "</div>";
$html .= "</div>";
    $html .= "<div id=\"ajout\" style=\"padding-top:10px;\">";

        $html .= "<form method=POST action=$script_upd_appart accept-charset=\"UTF-8\">";

        $html .= "<input name=\"action\" type=\"hidden\" value=\"1\">";

        $html .= "<div class=\"row mb10\">";
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"date_achat\" class=\"myFormLabels\">Acheté le...</label>
                    <input name=\"date_achat\" id=\"date_achat\" class=\"form-control editable-contrat\" type=\"date\" value=\"\">
                    <span name=\"startDateSelected\" id=\"startDateSelected\"></span>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-3\">";
                    $html .= "<label for=\"adresse\" class=\"myFormLabels\">Numéro et rue</label>";
                    $html .= "<input id=\"adresse\" type=\"text\" class=\"form-control\" name=\"adresse\" required>";
                $html .= "</div>";
            
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"code_postal\" class=\"myFormLabels\">Code postal</label>";
                    $html .= "<input id=\"code_postal\" type=\"text\" class=\"form-control\" name=\"code_postal\" required>";
                $html .= "</div>";

                $html .= "<div class=\"col-12 col-md-3\">";
                    $html .= "<label for=\"ville\" class=\"myFormLabels\">Ville</label>";
                    $html .= "<input id=\"ville\" type=\"text\" class=\"form-control\" name=\"ville\" required>";
                $html .= "</div>";

               

        $html .= "</div>";
        $html .= "<div class=\"row mb10\">";

        $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"loyer_nu\" class=\"myFormLabels\">Loyer hors charges</label>";
                    $html .= "<input type=\"number\" step=\".01\" class=\"form-control\" name=\"loyer_nu\" required>";
                $html .= "</div>";
            
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"charges\" class=\"myFormLabels\">Charges</label>";
                    $html .= "<input type=\"number\" step=\".01\" class=\"form-control\" name=\"charges\" required>";
                $html .= "</div>";


        $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
        $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Ajouter</button>";
    $html .= "</div>";

        $html .= "</div>";

        $html .= "</form>";


    $html .= "</div>";

    $html .= "<br /><br />";


    $apparts = $wpdb->get_results("select * from " . $wpdb->prefix . "qtnc_appartements where id_proprio = $user->id and date_vente is NULL order by date_achat desc");

    if(count($apparts)>0){
    foreach($apparts as $appart){
        $total = $appart->loyer + $appart->charges;
        $html .= "<div class=\"row mt-2\">";
            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<img id=\"crayon_$appart->id\" onclick=\"showEditionRow($appart->id, true)\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"20\">";
                $html .= "<img id=\"croix_$appart->id\" onclick=\"showEditionRow($appart->id, false)\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"20\">";
                $html .= "<span style=\"margin-left:5px;\">" . $appart->adresse . ", $appart->cp $appart->ville</span>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-2\">";
                $html .= "<span style=\"margin-left:5px;\">Acheté le " . date('d/m/Y', strtotime($appart->date_achat)) ."</span>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-2\">";
                $html .= "<span style=\"margin-left:5px;\">$appart->loyer € + $appart->charges € ($total €)</span>";
            $html .= "</div>";
        $html .= "</div>";

        $html .= "<div id=\"edition_$appart->id\" class=\"mt-2\" style=\"display:none;\">";
        $html .= "<div class=\"row\">";
            $html .= "<div class=\"col-12\">";
                $html .= "<form method=POST action=$script_upd_appart accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$appart->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$appart->id\">";
                        $html .= "<div class=\"row mt-2\">";
                            $html .= "<div class=\"col-12 col-md-3\">";
                                $html .= "<label for=\"adresse\" class=\"myFormLabels\">N° et rue</label>";
                                $html .= "<input id=\"adresse\" type=\"text\" class=\"form-control\" name=\"adresse\" value=\"$appart->adresse\" required>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-2\">";
                                $html .= "<label for=\"code_postal\" class=\"myFormLabels\">Code postal</label>";
                                $html .= "<input id=\"code_postal\" type=\"text\" class=\"form-control\" name=\"code_postal\" value=\"$appart->cp\" required>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-2\">";
                                $html .= "<label for=\"ville\" class=\"myFormLabels\">Ville</label>";
                                $html .= "<input id=\"ville\" type=\"text\" class=\"form-control\" name=\"ville\" value=\"$appart->ville\" required>";
                            $html .= "</div>";

                            $html .= "<div class=\"col-12 col-md-3\">";
                            $html .= "<label for=\"date_achat\" class=\"myFormLabels\">Acheté le...</label>
                                <input name=\"date_achat\" id=\"date_achat\" class=\"form-control\" type=\"date\" value=\"$appart->date_achat\">
                                <span name=\"startDateSelected_$appart->id\" id=\"startDateSelected_$appart->id\"></span>";
                            $html .= "</div>";

                            
                        $html .= "</div>";
                        $html .= "<div class=\"row mt-2\">";

                        $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"loyer_nu\" class=\"myFormLabels\">Loyer hors charges</label>";
                    $html .= "<input type=\"number\" step=\".01\" class=\"form-control\" name=\"loyer_nu\" value=\"$appart->loyer\" required>";
                $html .= "</div>";
            
                $html .= "<div class=\"col-12 col-md-2\">";
                    $html .= "<label for=\"charges\" class=\"myFormLabels\">Charges</label>";
                    $html .= "<input type=\"number\" step=\".01\" class=\"form-control\" name=\"charges\" value=\"$appart->charges\" required>";
                $html .= "</div>";

                        $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                                $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Modifier</button>";
                            $html .= "</div>";

                        $html .= "</div>";

                $html .= "</form>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "<div class=\"row mt-2\">";
        $html .= "<div class=\"col-12\">";
                $html .= "<form method=POST action=$script_upd_appart accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"-1\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$appart->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$appart->id\">";
                    $html .= "<div class=\"row mt-2\">";
                        $html .= "<div class=\"col-12 col-md-2\">";
                            $html .= "<label for=\"date_vente\" class=\"myFormLabels\">Vendu le...</label>
                                <input name=\"date_vente\" id=\"date_vente\" class=\"form-control\" type=\"date\">
                                <span name=\"endDateSelected_$appart->id\" id=\"endDateSelected_$appart->id\"></span>";
                            $html .= "</div>";

                        $html .= "<div class=\"col-12 col-md-2\">";
                            $html .= "<button type=\"submit\" class=\"btn btn-danger\" style=\"margin-top: 25px; width:100%;\">Enregistrer la vente</button>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";
            $html .= "</div>";
        $html .= "</div>";

    }
}else{
    $html .= "<div class=\"row mb10\">";
                    $html .= "<div class=\"col-12\">";
                    $html .= "Aucun appartement saisi pour l'instant.";
                    $html .= "</div>";

            $html .= "</div>";
}




    $html .= "</div>";
}
else{
    $html = "erreur";
}


echo $html;


?>