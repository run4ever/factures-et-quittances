<?php
require(dirname(__FILE__) . '/../../../../wp-load.php');

// Espace locataire : liste des quittances, accessible par lien secret (sans compte)
global $wpdb;

header("X-Robots-Tag: noindex, nofollow");
header("Referrer-Policy: no-referrer");

$token = (isset($_GET['acces']) && is_string($_GET['acces'])) ? $_GET['acces'] : "";
$locataire = null;
if(strlen($token) == 64){
    $locataire = $wpdb->get_row($wpdb->prepare(
        "SELECT t1.id, t1.uuid, t1.locataire, t2.adresse, t2.cp, t2.ville FROM " . $wpdb->prefix . "qtnc_locataires as t1 LEFT JOIN " . $wpdb->prefix . "qtnc_appartements as t2 on t1.id_appartement = t2.id WHERE t1.token_acces = %s",
        $token
    ));
}

$loyers = array();
if($locataire){
    $loyers = $wpdb->get_results($wpdb->prepare(
        "SELECT uuid, period_from, period_to, loyer_nu, charges FROM " . $wpdb->prefix . "qtnc_loyers WHERE id_locataire = %d ORDER BY period_from DESC",
        $locataire->id
    ));
}else{
    status_header(404);
}
$wpdb->close();

$url_pdf = plugins_url('quittances') . "/scripts/quitpdf.php";
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Mes quittances de loyer</title>
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f6f6f4; color: #222; margin: 0; padding: 16px; }
    main { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 24px; }
    h1 { font-size: 1.4rem; margin: 0 0 4px; }
    .adresse { color: #666; margin: 0 0 24px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 10px 6px; border-bottom: 1px solid #eee; }
    th { font-size: .85rem; color: #666; font-weight: 600; }
    td.montant { white-space: nowrap; }
    td.actions { text-align: right; white-space: nowrap; }
    a.bouton { display: inline-block; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: .9rem; background: #198754; color: #fff; margin-left: 4px; }
    a.bouton.secondaire { background: #e9ecef; color: #222; }
    .vide { color: #666; }
    @media (max-width: 520px) { td.actions a.bouton { display: block; margin: 4px 0 0; text-align: center; } }
</style>
</head>
<body>
<main>
<?php if(!$locataire){ ?>
    <h1>Lien invalide</h1>
    <p>Ce lien n'est pas ou plus valide. Utilisez le lien présent dans le dernier e-mail de quittance reçu, ou contactez votre propriétaire.</p>
<?php }else{ ?>
    <h1>Quittances de loyer de <?php echo esc_html($locataire->locataire); ?></h1>
    <p class="adresse"><?php echo esc_html(trim($locataire->adresse . ", " . $locataire->cp . " " . $locataire->ville, " ,")); ?></p>
    <?php if(count($loyers) == 0){ ?>
        <p class="vide">Aucune quittance disponible pour le moment.</p>
    <?php }else{ ?>
    <table>
        <thead><tr><th>Période</th><th>Montant</th><th></th></tr></thead>
        <tbody>
        <?php foreach($loyers as $loyer){
            $lien = $url_pdf . "?locataire=" . urlencode($locataire->uuid) . "&loyer=" . urlencode($loyer->uuid) . "&acces=" . urlencode($token);
            $periode = "Du " . date('d/m/Y', strtotime($loyer->period_from)) . " au " . date('d/m/Y', strtotime($loyer->period_to));
            $total = $loyer->loyer_nu + $loyer->charges;
        ?>
            <tr>
                <td><?php echo esc_html($periode); ?></td>
                <td class="montant"><?php echo esc_html($total); ?> €</td>
                <td class="actions">
                    <a class="bouton secondaire" href="<?php echo esc_url($lien . "&way=1"); ?>" target="_blank" rel="noopener">Voir</a>
                    <a class="bouton" href="<?php echo esc_url($lien . "&way=2"); ?>">Télécharger</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
<?php } ?>
</main>
</body>
</html>
