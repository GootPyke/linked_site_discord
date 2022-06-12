<?php 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', '968151234106241034');
define('OAUTH2_CLIENT_SECRET', 'YrhqGeFPWUEwcYKP_3dgXJkAjQ490rzM');

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

require_once 'src/controller/connexionController.php';
require_once 'src/controller/actualitesController.php';


    function accueil(){

        $title = "Accueil";
        ob_start();
?>
        <div id="accueil">
            <p id="p-titre">Le portail web pour tous les membres du serveur Discord</p>
            <p id="p-texte">Texte d'accroche</p>
        </div>
<?php 
        $content = ob_get_clean();
        require_once('src/vue/template.php');
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
                    'scope' => 'identify guilds'
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
                    $_SESSION['user'] = apiRequest($apiURLBase);

                    $_SESSION["pseudo"] = $_SESSION['user']->username;

                    // header('Location: src/vue/tableauDeBord/tableauDeBord.php');
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
                break;
        }
    } else {
        header('Location: index.php?action=accueil');
    }
?>

