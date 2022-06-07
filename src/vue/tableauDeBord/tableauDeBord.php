<?php 
    $title = "Tableau de bord";

    $pseudo = $_SESSION["pseudo"];

    echo"Bonjour ". $pseudo;
?>
ça marche !

<?php 
    $content = ob_get_clean();
    require_once '../template.php';
?>