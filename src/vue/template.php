<?php 
    if (isset($_SESSION["username"])) {
        $verif = true;
        var_dump($_SESSION["username"]);
    } else {
        $verif = false;
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="src/styles/general.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li>
                    <a href="index.php?action=accueil">Accueil</a>
                </li>

                <li>
                    <a href="#">Actualités</a>
                </li>

                <li>
                    <a href="#">Règlement</a>
                </li>

                <li>
                    <a href="#">Rôles</a>
                </li>

                <li>
                    <a href="#">Modération</a>
                </li>
            </ul>

            <?php 
            if ($verif===true) {
            ?>
            <li>
                <a href="#">
                    <?php 
                        echo"{$username}";
                        var_dump($username);
                    ?>
                    
                </a>
            </li>
            <?php 
            } else {
            ?>
            <li id='se-co'>
                <a id='se-co2' href="index.php?action=connexion">
                    Se connecter
                </a>
            </li>
            <?php 
            }
            ?>
        </nav>
    </header>
    
    <?= $content ?>
</body>
</html>