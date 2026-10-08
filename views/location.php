<?php

require(dirname(__FILE__) . '/../../../../wp-load.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(is_user_logged_in()){

    $user = wp_get_current_user();
    if ( in_array( 'proprietaire', (array) $user->roles ) ) {
        $html = do_shortcode("[dashboard lecteur='proprietaire']");
    }
    if ( in_array( 'locataire', (array) $user->roles ) ) {
        $html = do_shortcode("[dashboard lecteur='locataire']");
    }
    if ( in_array( 'administrator', (array) $user->roles ) ) {
        $html = do_shortcode("[dashboard lecteur='proprietaire']");
    }
}
else{
    $html = "erreur";
}


echo $html;


?>