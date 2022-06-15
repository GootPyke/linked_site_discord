<?php 
    require_once 'src/controller/connexionController.php';

    function verificationModeration(){
        if (verifier()) {
            $moderation = false;
            $rolesUser = $_SESSION["guildUserInfo"]->roles;
            $rolesServer = $_SESSION["guildRoles"];

            foreach ($rolesUser as $roleUser){
                foreach ($rolesServer as $roleServer){
                    if ($roleUser === $roleServer->id){
                        $moderation = true;
                        break 2;
                    } 
                }
            }

            return $moderation;
        }
    }
?>