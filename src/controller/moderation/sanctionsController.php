<?php
    require_once SITE_ROOT . '/src/model/moderation/sanctionsModel.php';
    require_once SITE_ROOT . '/src/services/moderation/donneesModerationService.php';
    require_once SITE_ROOT . '/src/controller/moderation/utilisateurSanctionneController.php';

    function banMember($userId, $reason){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/bans/" . $userId;

        apiRequest2($apiUrl, 'PUT', array(
            'reason' => $reason
        ));

        $membre = getMemberByDiscordId($userId);
        $username = $membre->user->username;
        $discriminator = $membre->user->discriminator;
        $avatar = $membre->user->avatar;
        $dateSanction = new DateTime();
        $dateSanctionBDD = $dateSanction->format('Y-m-d H:i:s');

        addToDataUtilisateurSanctionne($userId, $username, $discriminator, $avatar);
        addSanction($userId, "Bannissement", $reason, $dateSanctionBDD);
    }

    function kickMember($userId, $reason){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/members/" . $userId;
        
        apiRequest2($apiUrl, 'DELETE');

        $membre = getMemberByDiscordId($id);
        $username = $membre->user->username;
        $discriminator = $membre->user->discriminator;
        $avatar = $membre->user->avatar;
        $dateSanction = new DateTime();
        $dateSanctionBDD = $dateSanction->format('Y-m-d H:i:s');

        addToDataUtilisateurSanctionne($userId, $username, $discriminator, $avatar);
        addSanction($userId, "Expulsion", $reason, $dateSanctionBDD);
    }

    function warnMember($userId, $reason){
        $membre = getMemberByDiscordId($id);
        $username = $membre->user->username;
        $discriminator = $membre->user->discriminator;
        $avatar = $membre->user->avatar;
        $dateSanction = new DateTime();
        $dateSanctionBDD = $dateSanction->format('Y-m-d H:i:s');

        addToDataUtilisateurSanctionne($userId, $username, $discriminator, $avatar);
        addSanction($userId, "Avertissement", $reason, $dateSanctionBDD);
    }

    function sanctionnerMembre($idDiscord, $raison, $typeSanction){
        $membre = getMemberByDiscordId($id);
        $username = $membre->user->username;
        $discriminator = $membre->user->discriminator;
        $avatar = $membre->user->avatar;
        $dateSanction = new DateTime();
        $dateSanctionBDD = $dateSanction->format('Y-m-d H:i:s');

        switch ($typeSanction) {
            case 'Bannissement':
                $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/bans/" . $idDiscord;
                addToDataUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar);
                addSanction($idDiscord, "Avertissement", $reason, $dateSanctionBDD);
                apiRequest2($apiUrl, 'PUT', array(
                    'reason' => $reason
                ));
                break;
            
            case 'Expulsion':
                $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/members/" . $idDiscord;
                addToDataUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar);
                addSanction($idDiscord, "Avertissement", $reason, $dateSanctionBDD);
                apiRequest2($apiUrl, 'DELETE');
                break;

            case 'Avertissement':
                addToDataUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar);
                addSanction($idDiscord, "Avertissement", $reason, $dateSanctionBDD);
                break;
        }

    }

    function debanMember($id, $reason){
        $apiUrl = API_REFERENCE . "guilds/" . ID_SERVEUR . "/bans/" . $id;

        apiRequest2($apiUrl, 'DELETE');
    }

    function sanctionner(){
        $id = $_GET["id"];
        $membre = getMemberByDiscordId($id);
        $pseudo = $membre->user->username;
        $discriminateur = '#'. $membre->user->discriminator;

        if ($_GET["sanction"] === 'bannir') {
            $nomAction = "Bannir ";
            $lienActionForm = "index.php?action=validerSanction&sanction=bannir&id={$id}";

        } else if ($_GET["sanction"] === 'expulser') {
            $nomAction = "Expulser ";
            $lienActionForm = "index.php?action=validerSanction&sanction=expulser&id={$id}";

        } else if ($_GET["sanction"] === 'avertir'){
            $nomAction = "Avertir ";
            $lienActionForm = "index.php?action=validerSanction&sanction=avertir&id={$id}";
        }
        
        require_once 'src/view/moderation/formSanctionView.php';
    }

    function validerSanction(){
        if ($_GET["sanction"] === 'bannir') {
            banMember($_GET["id"], $_POST["raison"]);
        } else if ($_GET["sanction"] === 'deban') {
            debanMember($_GET["id"], $_POST["raison"]);
        } else if ($_GET["sanction"] === 'expulser') {
            kickMember($_GET["id"], $_POST["raison"]);
        } else if ($_GET["sanction"] === 'avertir'){
            warnMember($_GET["id"], $_POST["raison"]);
        }
    }
?>