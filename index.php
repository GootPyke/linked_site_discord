<?php 
session_start();

//Initialisation des paramètres en cas d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 600); //600 seconds = 10 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

$config = include('config.php');

//Racine du site jusqu'à index.php
define('SITE_ROOT', __DIR__);
// Nb Membres par page - affichage modération
define('NB_MEMBRES_AFFICHAGE_MOD', $config->NB_MEMBRES_AFFICHAGE_MOD);
//Invitation Discord sur le serveur
define('INVITATION_SERVEUR', $config->INVITATION_SERVEUR);
//Id rôle modération sur SRV Discord
define('ID_MODERATION', $config->ID_MODERATION);
//Id SRV Discord
define('ID_SERVEUR', $config->ID_SERVEUR);
//Lien de base de l'API Discord
define('API_REFERENCE', $config->API_REFERENCE);
//Lien du CDN de Discord
define('CDN_AVATAR_REFERENCE', $config->CDN_AVATAR_REFERENCE);

define('OAUTH2_CLIENT_ID', $config->OAUTH2_CLIENT_ID);
define('OAUTH2_CLIENT_SECRET', $config->OAUTH2_CLIENT_SECRET);
define('BOT_TOKEN', $config->BOT_TOKEN);

//URL utilisés pour les requêtes API Utilisateurs - Request1
$authorizeURL = $config->authorizeURL;
$tokenURL = $config->tokenURL;
$apiURLUserBase = $config->apiURLUserBase;
$_SESSION['apiURLGuild'] = $config->apiURLGuild;
$_SESSION['apiURLGuildInfo'] = $config->apiURLGuildInfo;
$revokeURL = $config->revokeURL;

//URL utilisé pour une requête API BOT - Request2
$_SESSION['apiURLGuildRoles'] = $config->apiURLGuildRoles;

// Initialisation d'une variable utilisée pour les requêtes API de la modération
$_SESSION["essai"] = false;

//Appel des contrôleurs
require_once SITE_ROOT . '/src/controller/autres/connexionController.php';
require_once SITE_ROOT . '/src/controller/autres/roleController.php';
require_once SITE_ROOT . '/src/controller/actualites/actualitesController.php';
require_once SITE_ROOT . '/src/controller/moderation/moderationController.php';

    //Affichage page d'accueil
    function accueil(){

        $title = "Accueil";
        ob_start();
?>
        <div id="accueil">
            <p id="p-titre">Le portail web pour tous les membres du serveur Discord</p>
            <p id="p-texte"></p>
        </div>
<?php 
        $content = ob_get_clean();
        require_once('src/view/template.php');
    }

    if(isset($_GET["action"])){
        $getAction = htmlspecialchars($_GET["action"]); 
        $action = preg_replace('#&[a-z]*;#', '', $getAction);

        switch ($action) {
            case 'accueil':
                accueil();
                break;
            
            //***************************
            //* Connexion               *
            //***************************
            case 'connexionP1':
                $_SESSION['params'] = array(
                    'client_id' => OAUTH2_CLIENT_ID,
                    'redirect_uri' => 'https://localhost/sitediscord/index.php?action=connexionP2',
                    'response_type' => 'code',
                    'scope' => 'identify guilds guilds.members.read'
                );

                header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($_SESSION['params']));

                die();
                break;
            
            case 'connexionP2':
                if(get('code')) {
                    $_SESSION['token'] = apiRequest($tokenURL, array(
                    "grant_type" => "authorization_code",
                    'client_id' => OAUTH2_CLIENT_ID,
                    'client_secret' => OAUTH2_CLIENT_SECRET,
                    'redirect_uri' => 'https://localhost/sitediscord/index.php?action=connexionP2',
                    'code' => get('code')
                    ));

                    $_SESSION['logout_token'] = $_SESSION['token']->access_token;
                    $_SESSION['access_token'] = $_SESSION['token']->access_token;

                    header('Location: ' . $_SERVER['PHP_SELF']);
                }
                
                if(session('access_token')) {
                    $_SESSION['user'] = apiRequest($apiURLUserBase);

                    //Données concernant les serveurs sur lesquels l'utilisateur est présent
                    $_SESSION["dataGuild"] = apiRequest($_SESSION['apiURLGuild']);

                    //Données sur l'utilisateur présent dans le serveur ou non.
                    $_SESSION["dataGuildUserInfo"] = apiRequest($_SESSION['apiURLGuildInfo']);

                    //Données sur les rôles de l'utilisateur
                    $_SESSION["dataGuildRoles"] = apiRequest2($_SESSION['apiURLGuildRoles']);

                    $_SESSION["administrateur"] = verificationAdmin();
                    $_SESSION["moderation"] = verificationModeration();
                }
                break;

            //***************************
            //* Déconnexion             *
            //***************************
            case 'deconnexion':
                logout($revokeURL, array(
                    'token' => session('access_token'),
                    'token_type_hint' => 'access_token',
                    'client_id' => OAUTH2_CLIENT_ID,
                    'client_secret' => OAUTH2_CLIENT_SECRET,
                ));
                unset($_SESSION['access_token']);
                session_destroy();
                header('Location: ' . $_SERVER['PHP_SELF']);
                die();
                break;

            //***************************
            //* Actualités              *
            //***************************
            case 'actualites':
                showActualites();
                break;
            
            case 'formulaireActu':
                formActu();
                break;

            case 'validerActu':
                newOrEditActu();
                header("Location: index.php?action=actualites");
                break;

            case 'supprimerActu':
                supprimerActu();
                header("Location: index.php?action=actualites");
                break;
            
            //***************************
            //* Modération              *
            //***************************
            case 'moderation':
                if (isset($_SESSION["access_token"]) && $_SESSION["moderation"] === true) {
                    modMenu();
                } else {
                    header('Location: index.php?action=accueil');
                }
                break;

            default:
                header('Location: index.php?action=accueil');
                break;
        }
    } else {
        header('Location: index.php?action=accueil');
    }
?>
