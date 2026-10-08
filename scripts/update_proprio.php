<?php
require(dirname(__FILE__) . '/../../../../wp-load.php');

if(is_user_logged_in() && isset($_POST['action']) && is_numeric($_POST['action'])){
    global $wpdb;
    $user = wp_get_current_user();
    $extensions_valides = array('jpg','jpeg','gif','png','webp');

    switch ($_POST['action']) {
        case '0':
            $wpdb->update(
                $wpdb->prefix . "qtnc_proprietaires",
                array(
                    'nom_entete' => $_POST['entete'],
                    'nom_contenu' => $_POST['nom_court'],
                    'adresse' => $_POST['adresse'],
                    'signature_email' => $_POST['signature'],
                    'email' => $_POST['email'],
                    'email_nom' => $_POST['email_nom'],
                ),
                array(
                    'user_id' => $user->id,
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                ),
                array(
                    '%d',
                )
            );
            break;
        case '2':
            $repStockage = "../../../uploads/quittances/images/";
            if (is_dir($repStockage) == FALSE){mkdir($repStockage,0777,true);}
            
            if(isset($_FILES['photofile'])){
                switch($_FILES['photofile']['error']){
                    case 0:
                        if ($_FILES['photofile']['size'] > intval($_POST['MAX_FILE_SIZE'])) {echo "Le fichier est trop gros<br />";}
                        else{
                            $extension_upload = strtolower(  substr(  strrchr($_FILES['photofile']['name'], '.')  ,1)  );
                            if (in_array($extension_upload, $extensions_valides)) {
                                // on prepare le nouveau nom du fichier
                                $horo = date("YmdHis");
                                $newfilename = "proprio_" . $user->id . "_" . $horo;
                                $newfilenomextension = "{$newfilename}.{$extension_upload}";
                                $newfilepath = $repStockage.$newfilenomextension;
                                // on stocke le fichier sur le serveur
                                $resultatMove = move_uploaded_file($_FILES['photofile']['tmp_name'],$newfilepath);
                                $wpdb->update(
                                    $wpdb->prefix . "qtnc_proprietaires",
                                    array(
                                        'signature_pdf' => $newfilenomextension,
                                    ),
                                    array(
                                        'user_id' => $user->id,
                                    ),
                                    array(
                                        '%s',
                                    ),
                                    array(
                                        '%d',
                                    )
                                );
                            } 
                            else {echo "mauvaise extension<br />";}
                        }
                    break;
                }
            }
        break;
    }

    $wpdb->close();
    $urlRedirection = get_home_url() . "/quittances";
    Header("Location: $urlRedirection");
}

?>	