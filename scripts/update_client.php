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
            "SELECT count(*) FROM " . $wpdb->prefix . "qtnc_clients WHERE `uuid` = %s",
            array( $uuid )
        );
        $rez = $wpdb->get_var($sql);
        if($rez == 0){
            $alreadyExists = false;
        }
    }
    return $uuid;
}

function client_appartient_a_l_utilisateur() {
    global $wpdb;
    // Création : le client est rattaché d'office à l'utilisateur connecté
    if($_POST['action'] == '1'){return true;}
    // Modification ou archivage : le client doit appartenir à l'utilisateur
    return qtnc_appartient_a_l_utilisateur(
        "SELECT id_sa FROM " . $wpdb->prefix . "qtnc_clients WHERE uuid = %s AND id = %d",
        $_POST['uuid'], $_POST['id']
    );
}

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action']) && client_appartient_a_l_utilisateur()){
    global $wpdb;
    $user = wp_get_current_user();
    $urlRedirection = get_home_url() . "/clients";

    switch ($_POST['action']) {
        case '-1':
            $wpdb->update(
                $wpdb->prefix . "qtnc_clients",
                array(
                    'date_to' => date("Y-m-d"),
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
                $wpdb->prefix . "qtnc_clients",
                array(
                    'client' => $_POST['nom'],
                    'email' => $_POST['email'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%s',
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            break;

        case '1':
            $wpdb->insert(
                $wpdb->prefix . "qtnc_clients",
                array(
                    'uuid' => get_new_uuid(),
                    'id_sa' => $user->id,
                    'client' => $_POST['nom'],
                    'email' => $_POST['email'],
                    'date_from' => date("Y-m-d"),
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                )
            );
            $urlRedirection = get_home_url() . "/factures?cli=" . $wpdb->insert_id;
            break;
    }

    $wpdb->close();
    
    Header("Location: $urlRedirection");
}

?>	