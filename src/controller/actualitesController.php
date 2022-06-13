<?php 
    require_once 'src/model/actualitesModel.php';
    
    function showActualites(){
        $actualites = getAllActualites();

        require_once 'src/vue/actualites/actualitesVue.php';
    }

    function newOrEditActu(){
        if (isset($_GET['id'])) {
            $dateDerMod = date("Y-m-d H:i:s", time());
            editActualite($_GET['id'], $_POST['titreActu'], $_POST['texteActu'], $dateDerMod);
        } else {
            $dateCreation = date("Y-m-d H:i:s", time());
            $dateDerMod = date("Y-m-d H:i:s", time());

            addActualite($_POST['titreActu'], $_POST['texteActu'], $dateCreation, $dateDerMod);
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

        require_once "src/vue/actualites/formActu.php";
    }

    function supprimerActu(){
        $id = $_GET["id"];

        deleteActualite($id);
    }
?>