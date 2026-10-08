<?php

function logout_func( ) {

    $url = html_entity_decode(wp_logout_url(get_home_url()));
    //$url = wp_logout_url( '/quittances' );
    //$url = wp_loginout('/quittances');
    header("Location: $url");

}
add_shortcode( 'logout', 'logout_func' );


?>