<?php
require(dirname(__FILE__) . '/../../../../wp-load.php');

function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function get_new_uuid() {
    global $wpdb;
    $alreadyExists = true;
    while($alreadyExists){
        $uuid = guidv4();
        $sql = $wpdb->prepare(
            "SELECT count(*) FROM " . $wpdb->prefix . "qtnc_locataires WHERE `uuid` = %s",
            array( $uuid )
        );
        $rez = $wpdb->get_var($sql);
        if($rez == 0){
            $alreadyExists = false;
        }
    }
    return $uuid;
}

function locataire_appartient_a_l_utilisateur() {
    global $wpdb;
    // Modification ou départ : le locataire doit occuper un logement de l'utilisateur
    if($_POST['action'] != '1'){
        $ok = qtnc_appartient_a_l_utilisateur(
            "SELECT t2.id_proprio FROM " . $wpdb->prefix . "qtnc_locataires as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id WHERE t1.uuid = %s AND t1.id = %d",
            $_POST['uuid'], $_POST['id']
        );
        if(!$ok){return false;}
    }
    // Création ou modification : l'appartement choisi doit appartenir à l'utilisateur
    if($_POST['action'] == '0' || $_POST['action'] == '1'){
        return qtnc_appartient_a_l_utilisateur(
            "SELECT id_proprio FROM " . $wpdb->prefix . "qtnc_appartements WHERE id = %d",
            $_POST['appartement']
        );
    }
    return true;
}

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action']) && locataire_appartient_a_l_utilisateur()){
    global $wpdb;
    $user = wp_get_current_user();

    switch ($_POST['action']) {
        case '-1':
            $wpdb->update(
                $wpdb->prefix . "qtnc_locataires",
                array(
                    'date_to' => $_POST['date_depart'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                ),
                array(
                    '%s',
                    '%d',
                )
            );


            break;
        
        case '0':

            $wpdb->update(
                $wpdb->prefix . "qtnc_locataires",
                array(
                    'locataire' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'id_appartement' => $_POST['appartement'],
                    'date_from' => $_POST['debut_bail'],
                    'is_coloc' => $_POST['coloc'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%d',
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            break;

        case '1':
            $wpdb->insert(
                $wpdb->prefix . "qtnc_locataires",
                array(
                    'uuid' => get_new_uuid(),
                    'id_appartement' => $_POST['appartement'],
                    'locataire' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'date_from' => $_POST['debut_bail'],
                    'is_coloc' => $_POST['coloc'],
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                )
            );

            break;

        case '3':
            // Nouveau lien d'accès à l'espace locataire : l'ancien lien ne fonctionne plus
            qtnc_token_locataire($_POST['id'], true);
            $urlRedirection = get_home_url() . "/quittances?lien=nouveau";
            break;
    }

    $wpdb->close();
    if(!isset($urlRedirection)){
        $urlRedirection = get_home_url() . "/locataires";
    }
    Header("Location: $urlRedirection");
}

?>	