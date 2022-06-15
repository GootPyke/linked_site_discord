<?php 
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

    function apiRequest2($url, $post=FALSE, $headers=array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);

        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        $headers[] = 'Accept: application/json';

        $headers[] = 'Authorization: Bot ' . BOT_TOKEN;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return json_decode($response);
    }

    function verifier(){
        if (isset($_SESSION["guildUserInfo"]->roles)) {
            return true;
        } else {
            return false;
        }
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

