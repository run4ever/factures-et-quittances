<?php

function clients_func( ) {

require(dirname(__FILE__) . '/../../../../../wp-load.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(is_user_logged_in()){

    global $wpdb;
    require(dirname(__FILE__) . '/../../tools/enums.php');
    require(dirname(__FILE__) . '/../../tools/mymenu.php');
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', '','',true );
    wp_enqueue_script( 'clients', plugins_url($pluginName) . '/js/clients.js?1000', '','',true );

    $script_upd_client = plugins_url($pluginName) . "/scripts/update_client.php";

    $user = wp_get_current_user();
    $patients = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "qtnc_clients WHERE id_sa = $user->id AND date_to is NULL");

    $html = "<div class='wrap'>";
    $html .= display_menu();
    $html .= "<h2>Patients</h2>";
    $html .= "<br /><br />";

    $html .= "<div class=\"row tableTitle mt30\">";
        $html .= "<div class=\"col-11\">";
              $html .= "Ajouter un patient";
        $html .= "</div>";
    $html .= "</div>";

    //Formulaire ajout
    $html .= "<div id=\"ajout\" style=\"padding-top:10px;\">";
        $html .= "<form method=POST action=$script_upd_client accept-charset=\"UTF-8\">";

        $html .= "<input name=\"action\" type=\"hidden\" value=\"1\">";

        $html .= "<div class=\"row mb10\">";
            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<label for=\"nom\" class=\"myFormLabels\">Civ. Nom Prénom</label>";
                $html .= "<input id=\"nom\" type=\"text\" class=\"form-control\" name=\"nom\" required>";
            $html .= "</div>";

            $html .= "<div class=\"col-12 col-md-3\">";
                $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                $html .= "<input id=\"email\" type=\"email\" class=\"form-control\" name=\"email\" required>";
            $html .= "</div>";

            $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Ajouter</button>";
            $html .= "</div>";

        $html .= "</div>";
        $html .= "</form>";
    $html .= "</div>";

    $html .= "<br /><br />";

    foreach($patients as $client){
        $html .= "<div class=\"row mt-4 mb-4\">";
            $html .= "<div class=\"col-12 col-md-6\">";
                $html .= "<img id=\"crayon_$client->id\" onclick=\"showEditionRow($client->id, true)\" src=\"" . plugins_url($pluginName) . "/images/crayon.png\" class=\"clicable\" width=\"20\">";
                $html .= "<img id=\"croix_$client->id\" onclick=\"showEditionRow($client->id, false)\" src=\"" . plugins_url($pluginName) . "/images/cancel.png\" class=\"clicable\" style=\"display:none;\" width=\"20\">";
                $html .= "<span style=\"margin-left:5px;\">$client->client";
                if(isset($client->email)){
                    $html .= " ($client->email)";
                }
                $html .= "</span>";
            $html .= "</div>";
        $html .= "</div>";

        $html .= "<div id=\"edition_$client->id\" class=\"row mt-2\" style=\"display:none;\">";
            $html .= "<div class=\"col-9\">";
                $html .= "<form method=POST action=$script_upd_client accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"0\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$client->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$client->id\">";
                        $html .= "<div class=\"row mt-2\">";
                            $html .= "<div class=\"col-12 col-md-5\">";
                                $html .= "<label for=\"nom\" class=\"myFormLabels\">Civ. Nom Prénom</label>";
                                $html .= "<input id=\"nom\" type=\"text\" class=\"form-control\" name=\"nom\" value=\"$client->client\" required>";
                            $html .= "</div>";
                            $html .= "<div class=\"col-12 col-md-5\">";
                                $html .= "<label for=\"email\" class=\"myFormLabels\">E-mail</label>";
                                $html .= "<input id=\"email\" type=\"email\" class=\"form-control\" name=\"email\" value=\"$client->email\" required>";
                            $html .= "</div>";
                            $html .= "<div class=\"col-12 col-md-2\" style=\"text-align:right;\">";
                                $html .= "<button type=\"submit\" class=\"btn btn-success\" style=\"margin-top: 25px; width:100%;\">Modifier</button>";
                            $html .= "</div>";
                        $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";
            $html .= "<div class=\"col-12 col-md-1\">";
                $html .= "<form method=POST action=$script_upd_client accept-charset=\"UTF-8\">";
                    $html .= "<input type=\"hidden\" name=\"action\" value=\"-1\">";
                    $html .= "<input type=\"hidden\" name=\"uuid\" value=\"$client->uuid\">";
                    $html .= "<input type=\"hidden\" name=\"id\" value=\"$client->id\">";
                    $html .= "<div class=\"row mt-2\">";
                        $html .= "<div class=\"col-12\">";
                            $html .= "<button type=\"submit\" class=\"btn btn-danger\" style=\"margin-top: 25px; width:100%;\">Supprimer</button>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</form>";
            $html .= "</div>";

        $html .= "</div>";

    }

    //fermeture du div wrap
    $html .= "</div>";
}
else{
    header("Location: " . wp_login_url(get_home_url() . "/clients"));
}

return $html;

}
add_shortcode( 'clients', 'clients_func' );

?>