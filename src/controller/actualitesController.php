<?php 
    require_once 'src/model/actualitesModel.php';
    
    function showActualites(){
        $actualites = getAllActualites();

        require_once 'src/vue/actualites/actualitesVue.php';
    }

    function newOrEditActu(){
        if (isset($_GET['id'])) {
            editActu($_GET['id'], $_POST['titreActu'], $_POST['texteActu'], $_POST['xxx'], $_POST['xxx']);
        } else {
            newActu($_POST['xxx'], $_POST['xxx'], $_POST['xxx'], $_POST['xxx']);
        }
    }

    function formActu(){
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $lienActionForm = "index.php?action=newOrEditActu";
            $nomAction = "Éditer une actualité";
            $titre = x;
            $texte = x;
        } else {
            
        }
    }
?>