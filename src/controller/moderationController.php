<?php 
    require_once 'src/controller/connexionController.php';

    function verifier(){
        if (isset($_SESSION["guildUserInfo"]->roles)) {
            return true;
        } else {
            return false;
        }
    }

    function verificationModeration(){
        if (verifier()) {
            $moderation = false;
            $rolesUser = $_SESSION["guildUserInfo"]->roles;
            $rolesServer = $_SESSION["guildRoles"];

            foreach ($rolesUser as $roleUser){
                foreach ($rolesServer as $roleServer){
                    if ($roleUser === $roleServer->id){
                        if ($roleServer->id === "941440986595336202") {
                            $moderation = true;
                            break 2;
                        }
                    } 
                }
            }
            return $moderation;
        }
    }

    function verificationModeration2($user){
        $rolesServer = $_SESSION["guildRoles"];

        foreach ($user->roles as $role){
            foreach ($rolesServer as $roleServer){
                if ($role === $roleServer->id) {
                    if ($role === "941440986595336202") {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function getMembers(){
        $apiURLMembers = 'https://discord.com/api/v10/guilds/'. 705530817039827026 .'/members?limit=1000';
        $membres = apiRequest2($apiURLMembers);
        $realMembers = array();

        foreach ($membres as $membre){
            if ((isset($membre->user->bot) === false) && verificationModeration2($membre) === false) {
                $realMembers[] = $membre;
            }
        }

        require 'src/vue/moderation/membresVue.php';
    }
?>