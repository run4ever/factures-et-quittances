<?php


function display_menu() {
  require(dirname(__FILE__) . '/enums.php');
  wp_enqueue_script( 'menu', plugins_url($pluginName) . '/js/menu.js?1009', '','',true );
  $padMenu = "2px 0px";

  //Sur mobile
  $html = "<div class=\"row d-flex d-md-none\">";
      $html .= "<div class=\"col-1\">";
      $html .= "</div>";

      $html .= "<div class=\"col-9\" style=\"text-align:center;\">";
          $html .= "<span style=\"font-weight:bold; font-size:16px;\">Quittances et factures</span>";
      $html .= "</div>";

      $html .= "<div class=\"col-1\">";
        $html .= "<img id=\"show_menu\" onclick=\"showMenu(true)\" src=\"/wp-content/plugins/quittances/images/burger.webp\" class=\"clicable\" width=\"30\">";
        $html .= "<img id=\"hide_menu\" onclick=\"showMenu(false)\" src=\"/wp-content/plugins/quittances/images/cross.webp\" class=\"clicable\" style=\"display:none;\" width=\"30\">";
      $html .= "</div>";

      $html .= "<div class=\"col-1\">";
      $html .= "</div>";

      $html .= "<hr style=\"margin-top:10px;\">";

      $html .= "<div id=\"phone_menu\" style=\"display:none;\">";
          $html .= "<div id=\"\" style=\"text-align:right;\">";
              $html .= "<div style=\"padding:$padMenu;\"><a href=\"/quittances\">Quittances</a></div>";
              $html .= "<div style=\"padding:$padMenu;\"><a href=\"/appartements\">Appartements</a></div>";
              $html .= "<div style=\"padding:$padMenu;\"><a href=\"/locataires\">Locataires</a></div>";
              $html .= "<div style=\"padding:$padMenu;\"><a href=\"/factures\">Factures</a></div>";
              $html .= "<div style=\"padding:$padMenu;\"><a href=\"/clients\">Patients</a></div>";
          $html .= "</div>";

          $html .= "<hr style=\"margin-top:10px;\">";
      $html .= "</div>";

  $html .= "</div>";
  

  //A partir de taille MD
  $html .= "<div class=\"row d-none d-md-flex\">";
        $html .= "<div class=\"col-3\">";
            $html .= "<h2>Quittances et factures</h2>";
        $html .= "</div>";
        $html .= "<div class=\"col-6\">";
        $html .= "</div>";
        $html .= "<div class=\"col-3\" style=\"display: flex;\">";

            //menu 1
            $html .= "<div style=\"display: flex-col;\">";
                $html .= "<div id=\"menu-1\">";
                    $html .= "<a href=\"/quittances\">Quittances</a>";
                $html .= "</div>";
                $html .= "<div id=\"sous-menu-1\" style=\"display: none; position:absolute; z-index:3; background-color:white; border: 1px solid grey;\">";
                    $html .= "<div style=\"display: flex-col; padding:10px;\">";
                        $html .= "<div style=\"\">";
                        $html .= "<a href=\"/appartements\">Appartements</a>";
                        $html .= "</div>";
                        $html .= "<div style=\"margin-top:10px;\">";
                        $html .= "<a href=\"/locataires\">Locataires</a>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</div>";
            $html .= "</div>";

            //menu 2
            $espaceLeft = "25px";
            $html .= "<div style=\"margin-left:$espaceLeft; display: flex-col;\">";
                $html .= "<div id=\"menu-2\">";
                    $html .= "<a href=\"/factures\">Factures</a>";
                $html .= "</div>";
                $html .= "<div id=\"sous-menu-2\" style=\"display: none; position:absolute; z-index:3; background-color:white; border: 1px solid grey;\">";
                    $html .= "<div style=\"display: flex-col; padding:10px;\">";
                        $html .= "<div style=\"\">";
                        $html .= "<a href=\"/clients\">Patients</a>";
                        $html .= "</div>";
                    $html .= "</div>";
                $html .= "</div>";
            $html .= "</div>";

            //Menu 3
            $html .= "<div style=\"margin-left:$espaceLeft;\">";
                $nonce = wp_create_nonce(-1);
                $html .= "<a href=\"/wp-login.php?action=logout&_wpnonce=$nonce\">Déconnexion</a>";
            $html .= "</div>";
        $html .= "</div>";
        $html .= "<hr>";
  $html .= "</div>";

  $html .= "<br />";

    return $html;

}


  

