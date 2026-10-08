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
            "SELECT count(*) FROM " . $wpdb->prefix . "qtnc_loyers WHERE `uuid` = %s",
            array( $uuid )
        );
        $rez = $wpdb->get_var($sql);
        if($rez == 0){
            $alreadyExists = false;
        }
    }
    return $uuid;
}

function loyer_appartient_a_l_utilisateur() {
    global $wpdb;
    if($_POST['action'] == '1' || $_POST['action'] == '2'){
        // Création : le locataire doit occuper un logement de l'utilisateur
        return qtnc_appartient_a_l_utilisateur(
            "SELECT t2.id_proprio FROM " . $wpdb->prefix . "qtnc_locataires as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id WHERE t1.id = %d",
            $_POST['locataire']
        );
    }
    // Modification ou suppression : le loyer doit concerner un logement de l'utilisateur
    return qtnc_appartient_a_l_utilisateur(
        "SELECT t3.id_proprio FROM " . $wpdb->prefix . "qtnc_loyers as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_locataires as t2 on t1.id_locataire = t2.id LEFT JOIN " . $wpdb->prefix . "qtnc_appartements as t3 on t2.id_appartement = t3.id WHERE t1.uuid = %s AND t1.id = %d",
        $_POST['uuid'], $_POST['id']
    );
}

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action']) && loyer_appartient_a_l_utilisateur()){
    global $wpdb;

    switch ($_POST['action']) {
        case '-1':
            $wpdb->delete(
                $wpdb->prefix . "qtnc_loyers",
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%d',
                )
            );


            break;
        
        case '0':

            $wpdb->update(
                $wpdb->prefix . "qtnc_loyers",
                array(
                    'period_from' => $_POST['date_from'],
                    'period_to' => $_POST['date_to'],
                    'loyer_nu' => $_POST['loyer_nu'],
                    'charges' => $_POST['charges'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
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
                $wpdb->prefix . "qtnc_loyers",
                array(
                    'id_locataire' => $_POST['locataire'],
                    'uuid' => get_new_uuid(),
                    'period_from' => $_POST['date_from'],
                    'period_to' => $_POST['date_to'],
                    'loyer_nu' => $_POST['loyer_nu'],
                    'charges' => $_POST['charges'],
                    'payed' => 1,
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                )
            );

            break;

        case '2':
            // Loyer reçu : enregistre le mois suivant le dernier loyer puis envoie la quittance
            $periode = qtnc_prochaine_periode($_POST['locataire']);
            // La période affichée sur le bouton doit être celle attendue (évite un double enregistrement)
            if(!$periode['actif']){
                $urlRedirection = get_home_url() . "/quittances?envoi=termine";
                break;
            }
            if($periode['from'] != $_POST['period_from']){
                $urlRedirection = get_home_url() . "/quittances?envoi=deja";
                break;
            }
            $locataire = $wpdb->get_row($wpdb->prepare(
                "SELECT t1.uuid, t2.loyer, t2.charges FROM " . $wpdb->prefix . "qtnc_locataires as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id WHERE t1.id = %d",
                $_POST['locataire']
            ));
            $new_uuid = get_new_uuid();
            $wpdb->insert(
                $wpdb->prefix . "qtnc_loyers",
                array(
                    'id_locataire' => $_POST['locataire'],
                    'uuid' => $new_uuid,
                    'period_from' => $periode['from'],
                    'period_to' => $periode['to'],
                    'loyer_nu' => $locataire->loyer,
                    'charges' => $locataire->charges,
                    'payed' => 1,
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                    '%d',
                )
            );
            $urlRedirection = plugins_url('quittances') . "/scripts/quitpdf.php?locataire=" . $locataire->uuid . "&loyer=" . $new_uuid . "&way=3";
            break;
    }

    $wpdb->close();
    if(!isset($urlRedirection)){
        $urlRedirection = get_home_url() . "/quittances";
    }
    Header("Location: $urlRedirection");
}

?>	