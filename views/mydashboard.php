<?php

class Replace_WP_Dashboard {

protected $capability = 'read';

protected $title;

final public function __construct() {
    if( is_admin() ) {
        add_action( 'init', array( $this, 'init' ) );
    }
}

final public function init() {
    if( current_user_can( $this->capability ) ) {
        $this->set_title();
        add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'current_screen', array( $this, 'current_screen' ) );
    }
}

/**
 * Sets the page title for your custom dashboard
 */
function set_title() {
    if( ! isset( $this->title ) ) {
       $this->title = __( 'Dashboard' );
    }
}

/**
 * Output the content for your custom dashboard
 */
function page_content() {
    $user = wp_get_current_user();
    if ( in_array( 'proprietaire', (array) $user->roles ) ) {
        $content = do_shortcode("[dashboard lecteur='proprietaire']");
    }
    if ( in_array( 'locataire', (array) $user->roles ) ) {
        $content = do_shortcode("[dashboard lecteur='locataire']");
    }
    if ( in_array( 'administrator', (array) $user->roles ) ) {
        $content = do_shortcode("[dashboard lecteur='proprietaire']");
    }

echo <<<HTML
{$content}
HTML;
}

/**
 * Fixes the page title in the browser.
 *
 * @param string $admin_title
 * @param string $title
 * @return string $admin_title
 */
final public function admin_title( $admin_title, $title ) {
    global $pagenow;
    if( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'tdb-quittances' == $_GET['page'] ) {
        $admin_title = $this->title . $admin_title;
    }
    return $admin_title;
}

final public function admin_menu() {

    /**
     * Adds a custom page to WordPress
     */
    add_menu_page( $this->title, '', $this->capability, 'tdb-quittances', array( $this, 'page_content' ) );

    /**
     * Remove the custom page from the admin menu
     */
    remove_menu_page('tdb-quittances');

    /**
     * Make dashboard menu item the active item
     */
    global $parent_file, $submenu_file;
    $parent_file = 'index.php';
    $submenu_file = 'index.php';

    /**
     * Rename the dashboard menu item
     */
    global $menu;
    $menu[2][0] = $this->title;

    /**
     * Rename the dashboard submenu item
     */
    global $submenu;
    $submenu['index.php'][0][0] = $this->title;

}

/**
 * Redirect users from the normal dashboard to your custom dashboard
 */
final public function current_screen( $screen ) {
    if( 'dashboard' == $screen->id ) {
        wp_safe_redirect( admin_url('admin.php?page=tdb-quittances') );
        exit;
    }
}

}