<?php
//require('../fpdf.php');
require(dirname(__FILE__) . '/../fpdf/fpdf.php');
require(dirname(__FILE__) . '/../../../../wp-load.php');

/****************
 * Exemples 
 * http://fpdf.org/en/script/script3.php
 * http://www.fpdf.org/en/tutorial/tuto5.htm
 * 
 *********************************/

    global $wpdb;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    class PDF extends FPDF
    {
        protected $B = 0;
        protected $I = 0;
        protected $U = 0;
        protected $HREF = '';
        
        function WriteHTML($html)
        {
            // Parseur HTML
            $html = str_replace("\n",' ',$html);
            $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
            foreach($a as $i=>$e)
            {
                if($i%2==0)
                {
                    // Texte
                    if($this->HREF)
                        $this->PutLink($this->HREF,$e);
                    else
                        $this->Write(5,$e);
                }
                else
                {
                    // Balise
                    if($e[0]=='/')
                        $this->CloseTag(strtoupper(substr($e,1)));
                    else
                    {
                        // Extraction des attributs
                        $a2 = explode(' ',$e);
                        $tag = strtoupper(array_shift($a2));
                        $attr = array();
                        foreach($a2 as $v)
                        {
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])] = $a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            }
        }
        
        function OpenTag($tag, $attr)
        {
            // Balise ouvrante
            if($tag=='B' || $tag=='I' || $tag=='U')
                $this->SetStyle($tag,true);
            if($tag=='A')
                $this->HREF = $attr['HREF'];
            if($tag=='BR')
                $this->Ln(5);
        }
        
        function CloseTag($tag)
        {
            // Balise fermante
            if($tag=='B' || $tag=='I' || $tag=='U')
                $this->SetStyle($tag,false);
            if($tag=='A')
                $this->HREF = '';
        }
        
        function SetStyle($tag, $enable)
        {
            // Modifie le style et sélectionne la police correspondante
            $this->$tag += ($enable ? 1 : -1);
            $style = '';
            foreach(array('B', 'I', 'U') as $s)
            {
                if($this->$s>0)
                    $style .= $s;
            }
            $this->SetFont('',$style);
        }
        
        function PutLink($URL, $txt)
        {
            // Place un hyperlien
            $this->SetTextColor(0,0,255);
            $this->SetStyle('U',true);
            $this->Write(5,$txt,$URL);
            $this->SetStyle('U',false);
            $this->SetTextColor(0);
        }

        
    }

    function moisAnneeFr($date){
        $mois="";
        switch(substr($date, 5, 2)) {
            case '01':
                $mois = "janvier";
                break;
            case '02':
                $mois = "février";
                break;
            case '03':
                $mois = "mars";
                break;
            case '04':
                $mois = "avril";
                break;
            case '05':
                $mois = "mai";
                break;
            case '06':
                $mois = "juin";
                break;
            case '07':
                $mois = "juillet";
                break;
            case '08':
                $mois = "août";
                break;
            case '09':
                $mois = "septembre";
                break;
            case '10':
                $mois = "octobre";
                break;
            case '11':
                $mois = "novembre";
                break;
            case '12':
                $mois = "décembre";
                break;
        }
        return $mois . " " . date('Y', strtotime($date));
    }

    function dateFrancaise($date){
        $jourNum = date('j', strtotime($date));
        if($jourNum == 1){$jourNum .= "er";}
        return $jourNum . " " . moisAnneeFr($date);
    }

    function get_month_in_french_text($i){
        $months[1] = "janvier";
        $months[2] = "février";
        $months[3] = "mars";
        $months[4] = "avril";
        $months[5] = "mai";
        $months[6] = "juin";
        $months[7] = "juillet";
        $months[8] = "août";
        $months[9] = "septembre";
        $months[10] = "octobre";
        $months[11] = "novembre";
        $months[12] = "décembre";
      
        return $months[$i];
      }

    function convert2DigitsNumberIntoLetters($number){

        $chiffreEnLettre[1] = "un";
        $chiffreEnLettre[2] = "deux";
        $chiffreEnLettre[3] = "trois";
        $chiffreEnLettre[4] = "quatre";
        $chiffreEnLettre[5] = "cinq";
        $chiffreEnLettre[6] = "six";
        $chiffreEnLettre[7] = "sept";
        $chiffreEnLettre[8] = "huit";
        $chiffreEnLettre[9] = "neuf";
        $chiffreEnLettre[10] = "dix";
        $chiffreEnLettre[11] = "onze";
        $chiffreEnLettre[12] = "douze";
        $chiffreEnLettre[13] = "treize";
        $chiffreEnLettre[14] = "quatorze";
        $chiffreEnLettre[15] = "quinze";
        $chiffreEnLettre[16] = "seize";
        $chiffreEnLettre[17] = "dix-sept";
        $chiffreEnLettre[18] = "dix-huit";
        $chiffreEnLettre[19] = "dix-neuf";
      
        $dizaine[1] = "dix";
        $dizaine[2] = "vingt";
        $dizaine[3] = "trente";
        $dizaine[4] = "quarante";
        $dizaine[5] = "cinquante";
        $dizaine[6] = "soixante";
        $dizaine[7] = "soixante-dix";
        $dizaine[8] = "quatre-vingt";
        $dizaine[9] = "quatre-vingt-dix";
      
        $nombre = intval($number);
      
        if($nombre > 0 && $nombre < 20){
          $nbEnLettres = $chiffreEnLettre[$nombre];
        }elseif(substr($number, 1, 1) == 0){
          $nbEnLettres = $dizaine[substr($number, 0, 1)];
        }else{
            $unite = substr($number, 1, 1);
            if(substr($number, 0, 1) == 7 || substr($number, 0, 1) == 9){
                $dizaine[7] = "soixante";
                $dizaine[9] = "quatre-vingt";
                $unite = 10 + intval($unite);
            }

            $nbEnLettres = $dizaine[substr($number, 0, 1)];
            if(substr($number, 1, 1) == 1){$nbEnLettres .= "-et";}
            $nbEnLettres .= "-" . $chiffreEnLettre[$unite];
        }
        return $nbEnLettres;
      }

      function convert3DigitsNumberIntoLetters($number){
        $centaines = "";
        switch(substr($number, 0, 1)){
            case 1:
                $centaines = "cent";
                break;
            case 2:
                $centaines = "deux cent";
                break;
            case 3:
                $centaines = "trois cent";
                break;
            case 4:
                $centaines = "quatre cent";
                break;
            case 5:
                $centaines = "cinq cent";
                break;
            case 6:
                $centaines = "six cent";
                break;
            case 7:
                $centaines = "sept cent";
                break;
            case 8:
                $centaines = "huit cent";
                break;
            case 9:
                $centaines = "neuf cent";
                break;
        }

        $centaines .= " " . convert2DigitsNumberIntoLetters(substr($number, 1, 2));
        return $centaines;

      }
      
      function convertDateIntoWords($date){
        $month = get_month_in_french_text(intval(substr($date, 5, 2)));
      
        $dateEnLettes = "deux mille";
        switch(substr($date, 1, 1)){
          case 1:
            $dateEnLettes .= " cent";
            break;
          case 2:
            $dateEnLettes .= " deux cent";
            break;
          default:
            break;
        }
        
        //ajout des 2 derniers chiffres de l'année
        $finAnnee = substr($date, 2, 2);
        $dateEnLettes .= convert2DigitsNumberIntoLetters($finAnnee);
      
        $dateEnLettes .= ", le ";
      
        //ajout du jour en lettre
        $dateEnLettes .= convert2DigitsNumberIntoLetters(substr($date, 8, 2));
      
        $dateEnLettes .= " " . $month;
      
        return $dateEnLettes;
      
      }

      function convertAmountIntoWords($amount){

        //on sépare les décimales
        $whole = floor($amount);      // partie entière
        $fraction = $amount - $whole; // partie decimale
        $wholeInWords = "";

        switch(strlen($whole)){
            case 2:
                $wholeInWords = convert2DigitsNumberIntoLetters($whole);
                break;
            case 3:
                $wholeInWords = convert3DigitsNumberIntoLetters($whole);
                break;
        }

        $totalInWords = $wholeInWords . " euros";
        
        if($fraction >0){
            $totalInWords .= " et " . convert2DigitsNumberIntoLetters($fraction) . " centimes";
        }

        return $totalInWords;
      }


    $loyerid = $_GET['loyer'];
    $locataireIdEnParam = $_GET['locataire'];
    $way = 1;
    if(isset($_GET['way'])){
        $way = $_GET['way'];
    }
    $loyer = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "qtnc_loyers WHERE uuid = %s", $loyerid));
    $pdf = new PDF();
            $pdf->SetMargins(15,20);
            $pdf->AddPage();

    if(!isset($loyer)){
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(180,10,mb_convert_encoding("Erreur 1",'ISO-8859-1', 'UTF-8'),0,1,'C');
    }
    else{
        $locataire = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "qtnc_locataires WHERE id = $loyer->id_locataire");


        if($locataireIdEnParam == $locataire->uuid){
            $appartement = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "qtnc_appartements WHERE id = $locataire->id_appartement");
            $proprio = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "qtnc_proprietaires WHERE user_id = $appartement->id_proprio");
        
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(180,10,mb_convert_encoding("Location meublée à titre de résidence principale",'ISO-8859-1', 'UTF-8'),0,1,'C');
            $pdf->SetFont('Arial','B',14);
            $pdf->SetFillColor(200,220,255);
            $pdf->Cell(180,15,mb_convert_encoding("Quittance de loyer - " . moisAnneeFr($loyer->period_from),'ISO-8859-1', 'UTF-8'),1,1,'C', true);
            $pdf->SetFont('Arial','',12);
            $pdf->Ln(5);
            
            $absX = $pdf->GetX();
            $ordY = $pdf->GetY();
        
            $pdf->Ln(10);
            $pdf->SetFont('Arial','I',12);
            $html = $proprio->nom_entete;
            $html = str_replace("’","'",$html);
            $pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
        
            $pdf->Ln(7);
            $html = $proprio->adresse;
            $html = str_replace("’","'",$html);
            $pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
        
            $pdf->Ln(10);
            $html = $locataire->locataire;
            //$html = str_replace("’","'",$html);
            //$pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'R');
        
            $pdf->Ln(7);
            $html = $appartement->adresse . ", "  . $appartement->cp . " " . $appartement->ville;
           // $html = str_replace("’","'",$html);
            //$pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'R');
        
            $pdf->SetFont('Arial','',12);
        
            $pdf->Ln(15);
            if(date('Y-m-d') < date('Y-m-d', strtotime(date('Y-m-d'). ' + 5 days'))){
                $html = "Fait à Paris, le " . dateFrancaise(date('Y-m-d'));
            }else{
                $html = "Fait à Paris, le " . dateFrancaise($loyer->period_from. ' + 5 days');
            }
            $html = str_replace("’","'",$html);
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'R');
        
        
            $pdf->Ln(10);
            $pdf->SetFont('Arial','BU',12);
            $html = "Adresse de la location :";
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');
        
            $pdf->Ln(7);
            $pdf->SetFont('Arial','',12);
            $html = $appartement->adresse . ", " . $appartement->cp . " " . $appartement->ville;
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');

            $total = number_format($loyer->loyer_nu + $loyer->charges,2);

            $pdf->Ln(15);
            $html = "Nous soussignés " . $proprio->nom_contenu . " propriétaires du logement désigné ci-dessus, déclarons avoir reçu de $locataire->locataire, la somme de ";
            /*
            if($locataire->id == 1){
                $html .= "sept cent quatre-vingt-dix euros";
            }elseif($locataire->id == 2){
                $html .= "six cent quatre-vingt-dix euros";
            }*/
            $html .= convertAmountIntoWords($total);
            $html .= " / $total euros, au titre du paiement du loyer et des charges pour la période de location du " . dateFrancaise($loyer->period_from) . " au " . dateFrancaise($loyer->period_to);
            if($locataire->is_coloc == 1){$html .= " et leur";}
            else{$html .= " et lui";}
            $html .= " en donnons quittance, sous réserve de tous nos droits.";
            $html = str_replace("’","'",$html);
            $pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
        
        
            $pdf->SetFont('Arial','BU',12);
            $pdf->Ln(10);
            $html = "Détail du règlement :";
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');
        
            $pdf->SetFont('Arial','',12);
            $pdf->Ln(7);
            $html = "Loyer :                           $loyer->loyer_nu euros";
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');
        
            $pdf->Ln(7);
            $html = "Provision pour charges : $loyer->charges euros";
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');
        
            $pdf->Ln(7);
            $pdf->SetFont('Arial','B',12);
            $html = "Total :                           $total euros";
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');
        
            $pdf->Ln(7);
            $pdf->SetFont('Arial','',12);
            $html = "Date du paiement : " . dateFrancaise($loyer->period_from. ' + 1 days');
            $pdf->Cell(180,5,mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'),0,0,'L');

            $image = "";
            if(isset($proprio->signature_pdf) && strlen($proprio->signature_pdf) > 0){
                $image = "../../../uploads/quittances/images/" . $proprio->signature_pdf;
            }else{
                $image = "../../../uploads/quittances/images/default.png";
            }
            //echo "<br />" . $image;
            //echo "<br /><img src=\"$image\">";
        
            $pdf->Ln(20);
            $pdf->Image($image,100,200,80);
            
            $pdf->Ln(55);
            $pdf->SetFont('Arial','',9);
            $html = "Cette quittance annule tous les reçus qui auraient pu être établis précédemment en cas de paiement partiel du montant du présent terme. Elle est à conserver pendant trois ans par le locataire (loi n° 89-462 du 6 juil. 1989 : art. 7-1).";
            $html = str_replace("’","'",$html);
            $pdf->WriteHTML(mb_convert_encoding($html,'ISO-8859-1', 'UTF-8'));
        }
        else{
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(180,10,mb_convert_encoding("Erreur 2",'ISO-8859-1', 'UTF-8'),0,1,'C');
        }
    }
    
    switch($way){
        case 1:
            $pdf->Output('I','quittance-'.str_replace(' ', '', $appartement->adresse).'-'.substr($loyer->period_from,0,4) . "-" . substr($loyer->period_from,5,2) . ".pdf");
            break;
        case 2:
            $pdf->Output('D','quittance-'.str_replace(' ', '', $appartement->adresse).'-'.substr($loyer->period_from,0,4) . "-" . substr($loyer->period_from,5,2) . ".pdf");
            break;
        case 3:
            $repStockage = "../../../uploads/quittances/" . substr($loyer->period_from,0,4) . "/" . substr($loyer->period_from,5,2) . "/";
            $attchmentName = 'quittance-'.str_replace(' ', '', $appartement->adresse).'-'.substr($loyer->period_from,0,4) . "-" . substr($loyer->period_from,5,2) . ".pdf";
            if (is_dir($repStockage) == FALSE){mkdir($repStockage,0777,true);}
            $pdf->Output('F',$repStockage . $attchmentName);
            $subject = "Quittance de loyer $appartement->adresse " . substr($loyer->period_from,0,4) . "-" . substr($loyer->period_from,5,2);
            $passage_ligne = "\r\n";
            $corps_message = "Bonjour,".$passage_ligne."Ci-joint votre quittance de loyer pour la période : ".moisAnneeFr($loyer->period_from) . "." . $passage_ligne."Cordialement,".$passage_ligne.$proprio->signature_email;

            $boundary = md5(rand()); // clé aléatoire de limite
            $passage_ligne = "\r\n";
            $type_fichier = "application/pdf";
            $handle = fopen($repStockage . $attchmentName, 'r'); //Ouverture du fichier
            $content = fread($handle, filesize($repStockage . $attchmentName)); //Lecture du fichier
            $encoded_content = chunk_split(base64_encode($content)); //Encodage
            $f = fclose($handle); //Fermeture du fichier

            
            $message = '--' . $boundary . $passage_ligne; //Séparateur d'ouverture
            $message .= "Content-Type: text/plain; charset=utf-8" . $passage_ligne; //Type du contenu
            $message .= "Content-Transfer-Encoding: 8bit" . $passage_ligne; //Encodage
            $message .= $passage_ligne . $corps_message . $passage_ligne; //Contenu du message
             
            $message .= $passage_ligne . "--" . $boundary . $passage_ligne; //Deuxième séparateur d'ouverture
            $message .= 'Content-type:'.$type_fichier.';name="'.$attchmentName.'"'. $passage_ligne; //Type de contenu (application/pdf ou image/jpeg)
            $message .='Content-Disposition: attachment; filename="'.$attchmentName.'"'. $passage_ligne; //Précision de pièce jointe
            $message .= 'Content-transfer-encoding:base64'. $passage_ligne; //Encodage
            $message .= $passage_ligne; //Ligne blanche. IMPORTANT !
            $message .= $encoded_content. $passage_ligne; //Pièce jointe

            $headers = "From: ". $proprio->email_nom . " <" . $proprio->email . ">" . $passage_ligne; //Emetteur
            $headers .= "Cc: $proprio->email" . $passage_ligne;
            $headers.= "Reply-to: ". $proprio->email_nom . " <" . $proprio->email . ">" . $passage_ligne; //Emetteur
            $headers.= "MIME-Version: 1.0" . $passage_ligne; //Version de MIME
            $headers.= 'Content-Type: multipart/mixed; boundary='.$boundary .' '. $passage_ligne; 

            mail($locataire->email, $subject, $message, $headers);
            $urlRedirection = get_home_url() . "/";
            Header("Location: $urlRedirection");
            break;
    }
      
?>