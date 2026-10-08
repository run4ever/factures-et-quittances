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
            "SELECT count(*) FROM " . $wpdb->prefix . "qtnc_factures WHERE `uuid` = %s",
            array( $uuid )
        );
        $rez = $wpdb->get_var($sql);
        if($rez == 0){
            $alreadyExists = false;
        }
    }
    return $uuid;
}

function facture_appartient_a_l_utilisateur() {
    global $wpdb;
    if($_POST['action'] == '1'){
        // Création : le client facturé doit appartenir à l'utilisateur
        return qtnc_appartient_a_l_utilisateur(
            "SELECT id_sa FROM " . $wpdb->prefix . "qtnc_clients WHERE id = %d",
            $_POST['patient']
        );
    }
    // Modification ou suppression : la facture doit concerner un client de l'utilisateur
    return qtnc_appartient_a_l_utilisateur(
        "SELECT t1.id_sa FROM " . $wpdb->prefix . "qtnc_clients as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_factures as t2 on t2.id_client = t1.id WHERE t2.uuid = %s AND t2.id = %d",
        $_POST['uuid'], $_POST['id']
    );
}

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action']) && facture_appartient_a_l_utilisateur()){
    global $wpdb;

    switch ($_POST['action']) {
        case '-1':
            $wpdb->delete(
                $wpdb->prefix . "qtnc_factures",
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            $urlRedirection = get_home_url() . "/factures";
            break;
        
        case '0':
            $wpdb->update(
                $wpdb->prefix . "qtnc_factures",
                array(
                    'date_facture' => $_POST['date_facture'],
                    'seances' => trim($_POST['seances']),
                    'total' => $_POST['total'],
                ),
                array(
                    'uuid' => $_POST['uuid'],
                    'id' => $_POST['id'],
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            $urlRedirection = get_home_url() . "/factures";
            break;

        case '1':
            $seances = "";
            $new_uuid = get_new_uuid();
            for ($i=1; $i < 51; $i++) { 
                if(isset($_POST['date_seance_'.$i])){
                    $date = $_POST['date_seance_'.$i];
                    $newDate = substr($date, 8, 2) . "/" . substr($date, 5, 2) . "/" . substr($date, 0, 4);
                    $seances .= $newDate . ",";
                }
            }
            $seances = substr($seances, 0, strlen($seances)-1);
            $wpdb->insert(
                $wpdb->prefix . "qtnc_factures",
                array(
                    'uuid' => $new_uuid,
                    'id_client' => $_POST['patient'],
                    'date_facture' => $_POST['date_facture'],
                    'seances' => $seances,
                    'total' => $_POST['total'],
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                )
            );
            $urlRedirection = get_home_url() . "/factures?newf=$new_uuid";
            //$urlRedirection = get_home_url() . "/wp-content/plugins/quittances/scripts/factpdf.php?facture=$new_uuid&way=1";
            break;
    }

    $wpdb->close();
    
    Header("Location: $urlRedirection");
}

?>	