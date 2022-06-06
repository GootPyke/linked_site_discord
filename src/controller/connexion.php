<?php 
    session_start();

    function connexion(){
    
        $provider = new \Wohali\OAuth2\Client\Provider\Discord([
            'clientId' => '968151234106241034',
            'clientSecret' => 'YrhqGeFPWUEwcYKP_3dgXJkAjQ490rzM',
            'redirectUri' => 'https://localhost/projet17/'
        ]);
    
        if (!isset($_GET['code'])) {
    
            // Step 1. Get authorization code
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
    
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
    
        } else {
            // Step 2. Get an access token using the provided authorization code
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
    
            // Show some token details
            echo '<h2>Token details:</h2>';
            echo 'Token: ' . $token->getToken() . "<br/>";
            echo 'Refresh token: ' . $token->getRefreshToken() . "<br/>";
            echo 'Expires: ' . $token->getExpires() . " - ";
            echo ($token->hasExpired() ? 'expired' : 'not expired') . "<br/>";

    
            // Step 3. (Optional) Look up the user's profile with the provided token
            try {
    
                $user = $provider->getResourceOwner($token);
    
                // echo '<h2>Resource owner details:</h2>';
                // printf('Hello %s#%s!<br/><br/>', $user->getUsername(), $user->getDiscriminator());
                // var_export($user->toArray());

                $_SESSION["token"] = $token->getToken();
                $_SESSION["username"] = $user->getUsername();
                $_SESSION["discriminator"] = $user->getDiscriminator();
    
                var_dump($_SESSION["token"]);
                var_dump($_SESSION["username"]);
                var_dump($_SESSION["discriminator"]);

                header('Location: src/vue/tableauDeBord/tableauDeBord.php');
            } catch (Exception $e) {
    
                // Failed to get user details
                exit('Oh dear...');
    
            }
        }

    }
?>