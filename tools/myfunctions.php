<?php


/* Disable WordPress Admin Bar for all users except administrators */
add_filter( 'show_admin_bar', 'restrict_admin_bar' );
function restrict_admin_bar( $show ) {
    return current_user_can( 'administrator' ) ? true : false;
}

//remplir user_details juste après l'enregistrement d'un nouveau user
add_action( 'user_register', 'create_proprio_details_record' );
function create_proprio_details_record($user_id) {

  global $wpdb;

  $user = get_userdata($user_id);

  if ( in_array( 'proprietaire', (array) $user->roles ) ) {

    $query_prenom = $wpdb->prepare(
        "SELECT meta_value FROM " . $wpdb->prefix . "usermeta WHERE `user_id` = %d AND `meta_key` = %s",
        array( $user_id, 'first_name' )
    );
    $prenom = $wpdb->get_var($query_prenom);

    $query_nom = $wpdb->prepare(
      "SELECT meta_value FROM " . $wpdb->prefix . "usermeta WHERE `user_id` = %d AND `meta_key` = %s",
      array( $user_id, 'last_name' )
    );
    $nom = $wpdb->get_var($query_nom);
    
    $aValues = array(
      'user_id' => $user_id,
      'email' => $user->user_email,
    );
    $aFormats = array(
      '%d',
      '%s',
    );

    if(isset($prenom) && isset($nom)){
      $aValues['signature_email'] = $prenom . " " . $nom;
      array_push($aFormats, '%s');
      $aValues['email_nom'] = $prenom . " " . $nom;
      array_push($aFormats, '%s');
    }
  
    $wpdb->insert(
      $wpdb->prefix . "qtnc_proprietaires",
      $aValues,
      $aFormats
  );

  }  


}

//supprimer certaines pages pour certains roles
add_action( 'admin_init', 'my_remove_menu_pages' );
function my_remove_menu_pages() {
  $user = wp_get_current_user();

  if ( in_array( 'proprietaire', (array) $user->roles ) ) {
   remove_menu_page('edit.php');
   remove_menu_page('post-new.php');
   remove_menu_page('media-new.php');
   remove_menu_page('edit.php?post_type=page');
   remove_menu_page('edit.php?post_type=project');
   remove_menu_page('edit-comments.php'); 
   remove_menu_page('profile.php'); 
   remove_menu_page('tools.php');
   remove_menu_page('upload.php');
   remove_menu_page('users.php');
   remove_menu_page('user-new.php');
   remove_menu_page('users.php');
   }

}

/* Creatation de nouveaux rôles */
add_role(
  'proprietaire', //  System name of the role.
  __( 'Propriétaire'  ), // Display name of the role.
  array(
      'read'  => true,
      'delete_posts'  => false,
      'delete_published_posts' => false,
      'edit_posts'   => false,
      'publish_posts' => false,
      'upload_files'  => true,
      'edit_pages'  => false,
      'edit_published_pages'  =>  false,
      'publish_pages'  => false,
      'delete_published_pages' => false, 
  )
);

//add custom menu capabilty to partner role
function proprio_roles()
{
    $role = get_role('proprietaire');
    $role->add_cap('menu_quittances_proprio', true);
}
add_action('init', 'proprio_roles', 11);

//add custom menu capabilty to administrator role
function myadmin_roles()
{
    $role = get_role('administrator');
    $role->add_cap('menu_quittances_proprio', true);
}
add_action('init', 'myadmin_roles', 11);

//create the locataire role
add_role(
  'locataire', //  System name of the role.
  __( 'Locataire'  ), // Display name of the role.
  array(
      'read'  => true,
      'delete_posts'  => false,
      'delete_published_posts' => false,
      'edit_posts'   => false,
      'publish_posts' => false,
      'upload_files'  => true,
      'edit_pages'  => false,
      'edit_published_pages'  =>  false,
      'publish_pages'  => false,
      'delete_published_pages' => false, 
  )
);

//add custom menu capabilty to administrator role
function locataire_roles()
{
    $role = get_role('locataire');
}
add_action('init', 'locataire_roles', 11);


// Modifier le logo sur la page de connexion
function wpm_login_style() { ?>
  <style type="text/css">
      #login h1 a, .login h1 a {
          background-image: url(<?php echo get_home_url(); ?>/wp-content/plugins/quittances/images/logo.jpg);
          background-size: 320px 154px;
      }

      .login h1 a {
        width: 320px !important;
        height: 154px !important;
      }

      body {
        background-color: #f6f6f4 !important; /* #FFF #04bbd4  */
      }
  </style>
<?php }
add_action( 'login_enqueue_scripts', 'wpm_login_style' );


//Login redirect
function redirect_on_login($user_login, $user)
{
  if(isset($_GET['redirect_to'])){
    $url = $_GET['redirect_to'];
  }else{
    $url = "/quittances";
  }
  //$userRedirect = 'admin.php?page=tdb-quittances';
  //$url = admin_url($userRedirect);
  wp_safe_redirect($url);
  exit();
}
add_filter('wp_login', 'redirect_on_login', 10, 2);

/*

add_filter( 'login_headerurl', 'my_custom_login_url' );
function my_custom_login_url($url) {
    return get_home_url();
}
*/
//redirection du lien Modifier le profil
/*
add_filter( 'edit_profile_url', 'my_custom_profile_url' );
function my_custom_profile_url($url) {
  $user = wp_get_current_user();
  if ( in_array( 'proprietaire', (array) $user->roles ) ) {
    return '/wp-admin/admin.php?page=tdb-quittances';
  }
}*/



/*
function hide_add_new() {
  global $submenu;
      // For Removing New Posts from Admin Menu
      unset($submenu['post-new.php?post_type=post'][10]);
      // For Removing New Pages
      unset($submenu['post-new.php?post_type=page'][10]);
     // For Removing CPTs
      unset($submenu['post-new.php?post_type=custom_post_type'][10]);
  }
  add_action('admin_menu', 'hide_add_new');
  */

  function remove_admin_bar_links() {
      global $wp_admin_bar;
      /*
      $wp_admin_bar->remove_menu('new-post');
      $wp_admin_bar->remove_menu('new-page');
      $wp_admin_bar->remove_menu('new-media');
      $wp_admin_bar->remove_menu('new-user');
      $wp_admin_bar->remove_menu('new-project');
      $wp_admin_bar->remove_menu('new-cpt');
      */
      $wp_admin_bar->remove_menu('new-content');
      $wp_admin_bar->remove_menu('comments');
      $wp_admin_bar->remove_menu('profile');
  }
  add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );
  
  

