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
            "SELECT count(*) FROM " . $wpdb->prefix . "qtnc_appartements WHERE `uuid` = %s",
            array( $uuid )
        );
        $rez = $wpdb->get_var($sql);
        if($rez == 0){
            $alreadyExists = false;
        }
    }
    return $uuid;
}

function appart_appartient_a_l_utilisateur() {
    global $wpdb;
    // Création : l'appartement est rattaché d'office à l'utilisateur connecté
    if($_POST['action'] == '1'){return true;}
    // Modification ou mise en vente : l'appartement doit appartenir à l'utilisateur
    return qtnc_appartient_a_l_utilisateur(
        "SELECT id_proprio FROM " . $wpdb->prefix . "qtnc_appartements WHERE uuid = %s AND id = %d",
        $_POST['uuid'], $_POST['id']
    );
}

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action']) && appart_appartient_a_l_utilisateur()){
    global $wpdb;
    $user = wp_get_current_user();

    switch ($_POST['action']) {
        case '-1':
            $wpdb->update(
                $wpdb->prefix . "qtnc_appartements",
                array(
                    'date_vente' => $_POST['date_vente'],
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
                $wpdb->prefix . "qtnc_appartements",
                array(
                    'adresse' => $_POST['adresse'],
                    'cp' => $_POST['code_postal'],
                    'ville' => $_POST['ville'],
                    'date_achat' => $_POST['date_achat'],
                    'loyer' => $_POST['loyer_nu'],
                    'charges' => $_POST['charges'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            break;

        case '1':
            $wpdb->insert(
                $wpdb->prefix . "qtnc_appartements",
                array(
                    'uuid' => get_new_uuid(),
                    'id_proprio' => $user->id,
                    'adresse' => $_POST['adresse'],
                    'cp' => $_POST['code_postal'],
                    'ville' => $_POST['ville'],
                    'date_achat' => $_POST['date_achat'],
                    'loyer' => $_POST['loyer_nu'],
                    'charges' => $_POST['charges'],
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                )
            );
            
            break;
    }

    $wpdb->close();
    $urlRedirection = get_home_url() . "/appartements";
    Header("Location: $urlRedirection");
}

?>	