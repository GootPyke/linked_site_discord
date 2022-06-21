<?php 
    require_once 'src/controller/connexionController.php';

    function verifier(){
        if (isset($_SESSION["guildUserInfo"]->roles)) {
            return true;
        } else {
            return false;
        }
    }

    function verificationModeration($user=false){
        if (verifier() === true) {
            $rolesUser = $_SESSION["guildUserInfo"]->roles;
            $rolesServer = $_SESSION["guildRoles"];
            $moderation = false;
    
            if ($user !== false) {
    
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
            } else {
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
        } else {
            exit();
        }
    }

    function verificationAdmin(){
        if (verifier()) {
            $guilds = $_SESSION["guild"];
            foreach ($guilds as $guild) {
                if ($guild->id === ID_SERVEUR) {
                    if (isset($guild->owner) === true && $guild->owner === true) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            exit();
        }
    }

    function banMember($userId, $reason){
        $apiUrl = "https://discord.com/api/v10/guilds/" . ID_SERVEUR . "/bans/" . $userId;

        apiRequest2($apiUrl, 'PUT', array(
            'reason' => $reason
        ));
    }

    function kickMember($userId){
        $apiUrl = "https://discord.com/api/v10/guilds/" . ID_SERVEUR . "/members/" . $userId;
        
        apiRequest2($apiUrl, 'DELETE');
    }

    function debanMember($id){
        $apiUrl = "https://discord.com/api/v10/guilds/" . ID_SERVEUR . "/bans/" . $id;

        apiRequest2($apiUrl, 'DELETE');
    }

    function getMemberByDiscordId($id){
        $apiUrl = "https://discord.com/api/v10/guilds/" . ID_SERVEUR . "/members/" . $id;

        $member = apiRequest2($apiUrl);

        return $member;
    }

    function sanctionner(){
        if ($_GET["sanction"] === 'bannir') {
            $id = $_GET["id"];
            $membre = getMemberByDiscordId($id);
            $nomAction = "Bannir " . $membre->user->username .'#'.$membre->user->discriminator;
            $lienActionForm = "index.php?action=validerSanction&sanction=bannir&id={$id}";

            require_once 'src/vue/moderation/formSanction.php';
        } elseif ($_GET["sanction"] === 'deban') {
            $id = $_GET["id"];
            // $membre = getMemberByDiscordId($id);
            // $nomAction = "Débannir " . $membre->user->username .'#'.$membre->user->discriminator;
            header("Location: index.php?action=validerSanction&sanction=deban&id={$id}");
        } else {
            $id = $_GET["id"];
            // $membre = getMemberByDiscordId($id);
            // $nomAction = "Expulser " . $membre->user->username.'#'.$membre->user->discriminator;
            header("Location: index.php?action=validerSanction&sanction=expulser&id={$id}");
        }

    }


    function validerSanction(){
        if ($_GET["sanction"] === 'bannir') {
            banMember($_GET["id"], $_POST["raison"]);
        } elseif ($_GET["sanction"] === 'deban') {
            debanMember($_GET["id"]);
        } else {
            kickMember($_GET["id"]);
        }
    }

    function getMembers($tri='nickname'){
        $apiURLMembers = 'https://discord.com/api/v10/guilds/'. ID_SERVEUR .'/members?limit=1000';
        $membres = apiRequest2($apiURLMembers);
        $realMembers = array();
        $nicknames = array();
        $discriminators = array();
        $tabTri = array();
        $varTri = array();

        foreach ($membres as $membre){
            if ((isset($membre->user->bot) === false) && verificationModeration($membre) === false) {
                $realMembers[] = $membre;
                $nicknames[] = $membre->user->username;
                $discriminators[] = $membre->user->discriminator;
            }
        }

        sort($nicknames, SORT_NATURAL);
        sort($discriminators, SORT_NUMERIC);

        if ($tri === 'nickname') {
            $tabTri = $nicknames;
        } else {
            $tabTri = $discriminators;
        }

        require 'src/vue/moderation/membresVue.php';
    }

    function getBannedMembers($tri='nickname'){
        $apiURLBanned = 'https://discord.com/api/v10/guilds/'. ID_SERVEUR .'/bans?limit=1000';
        $membresBannis = apiRequest2($apiURLBanned);
        $nicknames = array();
        $discriminators = array();
        $tabTri = array();
        $varTri = array();

        foreach ($membresBannis as $membreBanni){
            if ((isset($membreBanni->user->bot) === false)) {
                $nicknames[] = $membreBanni->user->username;
                $discriminators[] = $membreBanni->user->discriminator;
            }
        }

        sort($nicknames, SORT_NATURAL);
        sort($discriminators, SORT_NUMERIC);

        if ($tri === 'nickname') {
            $tabTri = $nicknames;
        } else {
            $tabTri = $discriminators;
        }

        require 'src/vue/moderation/membresBannisVue.php';
    }

    
?>