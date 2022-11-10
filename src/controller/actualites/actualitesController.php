<?php 
    require_once SITE_ROOT . '/src/model/actualites/actualitesModel.php';
    require_once SITE_ROOT . '/src/controller/moderation/moderationController.php';
    
    function showActualites(){
        $actualites = getAllActualites();
        $estAdmin = $_SESSION["administrateur"];

        require_once 'src/view/actualites/actualitesView.php';
    }

    function newOrEditActu(){
        if (isset($_GET['id'])) {

            $dateDerMod = new DateTime();
            $dateDerModBDD = $dateDerMod->format('Y-m-d H:i:s');

            editActualite($_GET['id'], $_POST['titreActu'], $_POST['texteActu'], $dateDerModBDD);
        } else {
            $dateCreation = new DateTime();
            $dateCreationBDD = $dateCreation->format('Y-m-d H:i:s');

            $dateDerMod = new DateTime();
            $dateDerModBDD = $dateDerMod->format('Y-m-d H:i:s');

            addActualite($_POST['titreActu'], $_POST['texteActu'], $dateCreationBDD, $dateDerModBDD);
        }
    }

    function formActu(){
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $actualite = getActualiteById($id);
            $lienActionForm = "index.php?action=validerActu&id={$id}";
            $nomAction = "Éditer une actualité";
            $titre = $actualite->getTitre();
            $texte = $actualite->getTexte();
        } else {
            $lienActionForm = "index.php?action=validerActu";
            $nomAction = "Nouvelle actualité";
            $titre = "";
            $texte = "";
        }

        require_once "src/view/actualites/formActuView.php";
    }

    function supprimerActu(){
        $id = $_GET["id"];

        deleteActualite($id);
    }
?>