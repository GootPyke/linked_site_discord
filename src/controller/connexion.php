<?php 
    function connexion1(){
        $params = array(
        'client_id' => OAUTH2_CLIENT_ID,
        'redirect_uri' => 'https://localhost/projet17Rework/index.php?action=connexionP2',
        'response_type' => 'code',
        'scope' => 'identify guilds'
        );

        header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));

        die();
    }

    function connexion2(){
        if(session('access_token')) {
            $user = apiRequest($apiURLBase);

            $_SESSION["username"] = $user->username;

            require_once 'src/vue/tableauDeBord/tableauDeBord.php';
            //     echo '<h3>Logged In</h3>';
            //     echo '<h4>Welcome, ' . $user->username . '</h4>';
            //     echo '<pre>';
            //         print_r($user);
            //     echo '</pre>';

            // } else {
            //     echo '<h3>Not logged in</h3>';
            //     echo '<p><a href="?action=login">Log In</a></p>';
        }
    }


    function deconnexion(){
        logout($revokeURL, array(
            'token' => session('access_token'),
            'token_type_hint' => 'access_token',
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
        ));
        unset($_SESSION['access_token']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    }

    // Fonctions utilisées dans d'autres fonctions

    function get($key, $default=NULL) {
        return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
    }

    function session($key, $default=NULL) {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    function apiRequest($url, $post=FALSE, $headers=array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);


        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        $headers[] = 'Accept: application/json';

        if(session('access_token'))
            $headers[] = 'Authorization: Bearer ' . session('access_token');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return json_decode($response);
    }

    function logout($url, $data=array()) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        CURLOPT_POSTFIELDS => http_build_query($data),
    ));
    $response = curl_exec($ch);
    return json_decode($response);
}
?>

