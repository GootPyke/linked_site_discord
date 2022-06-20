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
                    <a href="index.php?action=actualites">Actualités</a>
                </li>

                <!-- <li>
                    <a href="#">Règlement</a>
                </li>

                <li>
                    <a href="#">Rôles</a>
                </li> -->
                <?php 
                if (isset($_SESSION["user"]) && isset($_SESSION["mod"])){ 
                    if($_SESSION["mod"] === true){
                ?>
                <li>
                    <a href="index.php?action=moderation">Modération</a>
                </li>
                <?php 
                    }
                }
                ?>
            </ul>

            <?php 
            if (isset($_SESSION["user"])) {
                $gif = 'https://cdn.discordapp.com/avatars/' . $_SESSION["user"]->id . '/' . $_SESSION["user"]->avatar . '.gif';
            ?>
            <li id='li-user' onclick="window.location.href='index.php?action=deconnexion'">
                <?php 
                    if (strpos($gif, 'a_')) {
                ?>
                <a id='a-user'>
                    <?php 
                        echo '<img id="img-user" src="https://cdn.discordapp.com/avatars/'. $_SESSION["user"]->id. '/'. $_SESSION["user"]->avatar .'.gif"/>';
                    ?>
                </a>
                <?php 
                    } else {
                ?>
                <a id='a-user'>
                    <?php 
                        echo '<img id="img-user" src="https://cdn.discordapp.com/avatars/'. $_SESSION["user"]->id. '/'. $_SESSION["user"]->avatar .'.png"/>';
                    ?>
                </a>
                <?php 
                    }
                    echo "<h3 id='pseudo-user'><p>" . $_SESSION["user"]->username ."</p><p>#" . $_SESSION["user"]->discriminator . "</p></h3>"; 
                ?>
            </li>
            <?php 
            } else {
            ?>
            <li id='se-co'>
                <a id='se-co2' href="index.php?action=connexionP1">
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