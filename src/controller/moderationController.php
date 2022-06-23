<?php 
    require_once 'src/controller/connexionController.php';

    function dataGuilds(){
        if (session('access_token')) {
            $_SESSION["dataGuild"] = apiRequest($_SESSION['apiURLGuild']);
            $_SESSION["dataGuildUserInfo"] = apiRequest($_SESSION['apiURLGuildInfo']);
            $_SESSION["dataGuildRoles"] = apiRequest2($_SESSION['apiURLGuildRoles']);
        }
    }

    function verifier(){
        if ((isset($_SESSION["dataGuild"]) === true) && (isset($_SESSION["dataGuildUserInfo"]) === true) && (isset($_SESSION["dataGuildRoles"]) === true)) {
            return true;
        } else {
            if ($_SESSION["essai"] === true) {
                $_SESSION["essai"] = false;
                return false;
            } else {
                dataGuilds();
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
    
            if ($user !== false) {
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
            exit();
        }
    }

    function banMember($userId, $reason){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/bans/" . $userId;

        apiRequest2($apiUrl, 'PUT', array(
            'reason' => $reason
        ));
    }

    function kickMember($userId){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/members/" . $userId;
        
        apiRequest2($apiUrl, 'DELETE');
    }

    function debanMember($id){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/bans/" . $id;

        apiRequest2($apiUrl, 'DELETE');
    }

    function getMemberByDiscordId($id){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/members/" . $id;

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
        $apiURLMembers = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/members?limit=1000';
        $membres = apiRequest2($apiURLMembers);
        $realMembers = array();
        $nicknames = array();
        $discriminators = array();
        $tabTri = array();

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
        $apiURLBanned = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/bans?limit=1000';
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