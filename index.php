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
    
    require_once 'src/controller/connexion.php';

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
            
            case 'connexionP1':
                $params = array(
                    'client_id' => OAUTH2_CLIENT_ID,
                    'redirect_uri' => 'https://localhost/projet17Rework/index.php?action=connexionP2',
                    'response_type' => 'code',
                    'scope' => 'identify guilds'
                );

                header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));

                die();
                break;
            
            case 'connexionP2':
                if(get('code')) {
                    $token = apiRequest($tokenURL, array(
                    "grant_type" => "authorization_code",
                    'client_id' => OAUTH2_CLIENT_ID,
                    'client_secret' => OAUTH2_CLIENT_SECRET,
                    'redirect_uri' => 'https://localhost/projet17Rework/index.php?action=connexionP2',
                    'code' => get('code')
                    ));

                    $logout_token = $token->access_token;
                    $_SESSION['access_token'] = $token->access_token;

                    header('Location: ' . $_SERVER['PHP_SELF']);
                }
                
                if(session('access_token')) {
                    $user = apiRequest($apiURLBase);

                    $_SESSION["pseudo"] = $user->username;

                    header('Location: src/vue/tableauDeBord/tableauDeBord.php');
                }
                break;

            case 'deconnexion':
                logout($revokeURL, array(
                    'token' => session('access_token'),
                    'token_type_hint' => 'access_token',
                    'client_id' => OAUTH2_CLIENT_ID,
                    'client_secret' => OAUTH2_CLIENT_SECRET,
                ));
                unset($_SESSION['access_token']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                die();
                break;
        }
    } else {
        header('Location: index.php?action=accueil');
    }
?>

