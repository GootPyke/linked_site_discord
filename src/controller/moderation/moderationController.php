<?php
    require_once SITE_ROOT . '/src/controller/moderation/historiqueSanctionsController.php';
    require_once SITE_ROOT . '/src/controller/moderation/sanctionsController.php';
    require_once SITE_ROOT . '/src/controller/moderation/utilisateurSanctionneController.php';

    require_once SITE_ROOT . "/src/services/moderation/donneesModerationService.php";
    require_once SITE_ROOT . "/src/services/moderation/displaySpecificUsersService.php";

    function modMenu(){
        if (!isset($_SESSION["membres"])
        || !isset($_SESSION["membresBannis"])
        ) 
        {
            getDataForMod();
        }

        // Vérifier et actualiser les données des utilisateurs Discord sanctionnés sur le serveur.
        verifierUtilisateursSanctionnes();

        $getMenu = htmlspecialchars($_GET["menu"]); 
        $menu = preg_replace('#&[a-z]*;#', '', $getMenu);
        switch ($menu) {
            case 'membres':
            case 'utilisateursBannis':
                $tri = 'pseudoAZ';
                $page = 1;
                $recherche = false;
            
                if (isset($_POST["tri-sel"])) {
                    $tri = $_POST["tri-sel"];
                } else if(isset($_GET["tri"])){
                    $tri = $_GET["tri"];
                }
            
                if (isset($_GET["page"])) {
                    $page = $_GET["page"];
                }

                if(isset($_POST["recherche"])){
                    $recherche = $_POST["recherche"];
                } else if(isset($_GET["recherche"])){
                    $recherche = $_GET["recherche"];
                }

                if ($recherche == "") {
                    $recherche = false; 
                }
            
                if ($menu === 'membres') {
                    displaySpecificUsers($tri, $page, $recherche);
                } else {
                    displaySpecificUsers($tri, $page, $recherche, true);
                }
                
                break;
        
            case 'sanction':
                sanctionner();
                break;
        
            case 'validerSanction':
                validerSanction();
                header("Location: index.php?action=moderation");
                break;
        
            //Voir la raison du bannissement
            case 'voirRaison':
                $idSanction = htmlspecialchars($_GET["idSanction"]);
                voirRaison($idSanction);
                break;
        
            //Voir l'historique des sanctions
            case 'historiqueSanctions':
                $tri = "lastDESC";
                $page = 1;
                $filtre = "all";  
                $recherche = false;

                if (isset($_POST["tri-sel"])) {
                    $tri = $_POST["tri-sel"];
                } else if (isset($_GET["tri"])) {
                    $tri = $_GET["tri"];
                }
        
                if (isset($_GET["page"])) {
                    $page = $_GET["page"];
                } 
        
                if(isset($_POST["recherche"])){
                    $recherche = $_POST["recherche"];
                } else if(isset($_GET["recherche"])){
                    $recherche = $_GET["recherche"];
                }

                if ($recherche == "") {
                    $recherche = false; 
                }

                $banCheck = isset($_POST["ban-check"]);
                $expuCheck = isset($_POST["expu-check"]);
                $avertCheck = isset($_POST["avert-check"]);
        
                if ($banCheck || $expuCheck || $avertCheck) {
                    
                    if (($banCheck) && (!$expuCheck) && (!$avertCheck)){
                        $filtre = "banOnly";
                    } else if ((!$banCheck) && ($expuCheck) && (!$avertCheck)){
                        $filtre = "expuOnly";
                    } else if ((!$banCheck) && (!$expuCheck) && ($avertCheck)){
                        $filtre = "avertOnly";
                    } else if (($banCheck) && ($expuCheck) && (!$avertCheck)){
                        $filtre = "banExpu";
                    } else if (($banCheck) && (!$expuCheck) && ($avertCheck)){
                        $filtre = "banAvert";
                    } else if ((!$banCheck) && ($expuCheck) && ($avertCheck)){
                        $filtre = "expuAvert";
                    } 
                } else if (isset($_GET["filtre"])) {
                    $filtre = $_GET["filtre"];
                } else {
                    $filtre = "all";    
                }
        
                displayLastestSanctions($tri, $filtre, $page, $recherche);
                break;

                default:
                    header("Location: index.php?action=moderation&menu=membres");
                    break;
        }
    }

?>