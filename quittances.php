<?php
/*
Plugin Name: Quittances de loyer
Plugin URI: https://1dossard.com
Description: Shortcodes (raccourcis de code), scripts automatisés, etc. 
Version: 0.1
Author: Fabien Laurette
Author URI: https://1dossard.com
License: GPL2
*/
setlocale (LC_TIME, 'fr_FR.utf8','fra');
require(dirname(__FILE__) . '/../../../wp-load.php');

// include_once ("tools/mytools.php");

include_once ("tools/myfunctions.php");

//----------------------------------------------------------
// Mise à jour de la structure de la base
//----------------------------------------------------------
function qtnc_maj_base(){
    global $wpdb;
    if(get_option('qtnc_db_version') >= 1){return;}

    $table = $wpdb->prefix . "qtnc_entreprises";
    $colonnes = $wpdb->get_col("SHOW COLUMNS FROM $table");
    if(!in_array('adeli', $colonnes)){$wpdb->query("ALTER TABLE $table ADD COLUMN adeli VARCHAR(30) NULL");}
    if(!in_array('siret', $colonnes)){$wpdb->query("ALTER TABLE $table ADD COLUMN siret VARCHAR(30) NULL");}

    update_option('qtnc_db_version', 1);
}
qtnc_maj_base();

//----------------------------------------------------------
// Mon dashboard personnalisé
//----------------------------------------------------------
/*include_once ("views/mydashboard.php");
new Replace_WP_Dashboard();
include_once ("views/shortcodes/dashboard.php");
*/


// include_once ("public/views/profil.php");
// include_once ("public/views/consultants.php");

//css
wp_enqueue_style('nosstyles', plugins_url( ).'/quittances/css/quittances.css', array(), '0.0.002' ); 
wp_enqueue_style('bootstrap-min_css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', array(), false, 'all');

// wp_enqueue_style('actalan_admin', plugins_url().'/actalan/admin/css/actalan-admin.css', array(), '0.0.020' ); 





//----------------------------------------------------------
// Mon menu admin perso
//----------------------------------------------------------
// Hook for adding admin menus
/*
add_action('admin_menu', 'menu_quittances');


function menu_quittances() {
   // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, string $icon_url = ”, int|float $position = null ): string
    add_menu_page('Gestion Location', "Gestion Location", 'menu_quittances_proprio', 'location', 'menu_location', 'dashicons-building', 100);
    add_submenu_page('location', 'Mes locataires', "Locataires", 'menu_quittances_proprio', 'locataires', 'menu_locataires');
    add_submenu_page('location', 'Mes apparts', "Appartements", 'menu_quittances_proprio', 'apparts', 'menu_apparts');
   //pages admin sans item dans le menu de gauche :
   //add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = ”, int|float $position = null )
//    add_submenu_page('', 'Consultant', 'Consultant', 'menu_actalan_partner', 'consultant', 'menu_actalan_fiche_consultant', null);
//    add_submenu_page('', 'Nouvelle mission', 'Nouvelle mission', 'menu_actalan_partner', 'nouvelle-mission', 'ajouter_mission', null);
}
function menu_location(){require 'views/location.php';}
function menu_locataires(){require 'views/location_locataires.php';}
function menu_apparts(){require 'views/location_appartements.php';}
*/

// include_once ("public/shortcodes/oneprofile.php");
// include_once ("public/shortcodes/consultants.php");
// include_once ("public/shortcodes/publicprofile.php");

include_once ("views/shortcodes/quittances.php");
include_once ("views/shortcodes/appartements.php");
include_once ("views/shortcodes/locataires.php");
include_once ("views/shortcodes/factures.php");
include_once ("views/shortcodes/clients.php");

?>