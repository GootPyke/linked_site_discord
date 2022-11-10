<?php 
    function verifier(){
        if ((isset($_SESSION["dataGuild"]) === true) && (isset($_SESSION["dataGuildUserInfo"]) === true) && (isset($_SESSION["dataGuildRoles"]) === true)) {
            return true;
        } else {
            if ($_SESSION["essai"] === true) {
                $_SESSION["essai"] = false;
                return false;
            } else {
                $_SESSION["dataGuild"] = apiRequest($_SESSION['apiURLGuild']);

                $_SESSION["dataGuildUserInfo"] = apiRequest($_SESSION['apiURLGuildInfo']);

                $_SESSION["dataGuildRoles"] = apiRequest2($_SESSION['apiURLGuildRoles']);
                $_SESSION["essai"] = true;
                
                $resultat = verifier();
            }
        }
        return $resultat;
    }

    function verificationModeration($user=false){
        if (verifier() === true) {
            $rolesUser = $_SESSION["dataGuildUserInfo"]->roles;
            $rolesServer = $_SESSION["dataGuildRoles"];

            if (is_null($rolesUser) || is_null($rolesServer)) {
                return false;
            }
    
            if ($user !== false) {
                if(!isset($user->roles)) {
                    return false;
                }

                foreach ($user->roles as $role){
                    foreach ($rolesServer as $roleServer){
                        if ($role === $roleServer->id) {
                            if ($role === ID_MODERATION) {
                                return true;
                            }
                        }
                    }
                }
    
                return false;
            } else {
                foreach ($rolesUser as $roleUser){
                    foreach ($rolesServer as $roleServer){
                        if ($roleUser === $roleServer->id){
                            if ($roleServer->id === ID_MODERATION) {
                                return true;
                                break 2;
                            }
                        } 
                    }
                }
                return false;
            }
        } else {
            exit();
        }
    }

    function verificationAdmin(){
        if (verifier()) {
            $guilds = $_SESSION["dataGuild"];
            foreach ($guilds as $guild) {
                if ($guild->id === ID_SERVEUR) {
                    if (isset($guild->owner) === true && $guild->owner === true) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            return false;
        }
    }
?>