<?php 
session_start();

//Initialisation des paramètres en cas d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

$config = include('config.php');

//Id rôle modération sur SRV Discord
define('ID_MODERATION', $config->ID_MODERATION);
//Id SRV Discord
define('ID_SERVEUR', $config->ID_SERVEUR);
//Lien de base de l'API Discord
define('API_REFERENCE', $config->API_REFERENCE);

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

// Définition de la locale utilisateur à français (Utile dans les ActuModel)
setlocale(LC_TIME, 'french');

//Appel des contrôleurs
require_once 'src/controller/connexionController.php';
require_once 'src/controller/actualitesController.php';
require_once 'src/controller/moderationController.php';

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
                    'redirect_uri' => 'https://localhost/projet17/index.php?action=connexionP2',
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
                    'redirect_uri' => 'https://localhost/projet17/index.php?action=connexionP2',
                    'code' => get('code')
                    ));

                    $_SESSION['logout_token'] = $_SESSION['token']->access_token;
                    $_SESSION['access_token'] = $_SESSION['token']->access_token;

                    header('Location: ' . $_SERVER['PHP_SELF']);
                }
                
                if(session('access_token')) {
                    $_SESSION['user'] = apiRequest($apiURLUserBase);

                    $_SESSION["pseudo"] = $_SESSION['user']->username;

                    // $_SESSION["guild"] = apiRequest($_SESSION['apiURLGuild']);

                    // $_SESSION['guildUserInfo'] = apiRequest($_SESSION['apiURLGuild']Info);

                    // $_SESSION['guildRoles'] = apiRequest2($_SESSION['apiURLGuild']Roles);

                    $_SESSION["mod"] = verificationModeration();
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
                if (isset($_GET["tri"])) {
                    getMembers($_GET["tri"]);
                } else {
                    getMembers();
                }
                break;

            case 'sanction':
                sanctionner();
                break;

            case 'validerSanction':
                validerSanction();
                header("Location: index.php?action=moderation");
                break;

            case 'membresBannis':
                if (isset($_GET["tri"])) {
                    getBannedMembers($_GET["tri"]);
                } else {
                    getBannedMembers();
                }
                break;

            //Voir la raison du bannissement
            case 'viewRaison':
                viewRaison();
                break;

            //Voir l'historique des sanctions
            case 'histoSanc':
                break;
        }
    } else {
        header('Location: index.php?action=accueil');
    }
?>
