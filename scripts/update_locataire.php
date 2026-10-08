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

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action'])){
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
    }

    $wpdb->close();
    $urlRedirection = get_home_url() . "/locataires";
    Header("Location: $urlRedirection");
}

?>	