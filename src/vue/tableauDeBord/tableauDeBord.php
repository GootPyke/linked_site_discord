<?php 
    $title = "Tableau de bord";

    $username = $_SESSION["username"];
    $discriminator = $_SESSION["discriminator"];

    echo"Bonjour ". $username . $discriminator;
?>


<?php 
    $content = ob_get_clean();
    require_once '../template.php';
?>