<?php 
    $title = "Tableau de bord";

    $username = $_SESSION["username"];

    echo"Bonjour ". $username;
?>
test

<?php 
    $content = ob_get_clean();
    require_once '../template.php';
?>