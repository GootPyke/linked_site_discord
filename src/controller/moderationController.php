<?php 
    require_once 'src/controller/connexionController.php';
    require_once 'src/model/sanctionsModel.php';
    require_once 'src/controller/utilisateurSanctionneController.php';

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
            return false;
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

            require_once 'src/view/moderation/formSanctionView.php';
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

    function getMembers(){
        $apiURLMembers = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/members?limit=1000';
        $membres = apiRequest2($apiURLMembers);

        return $membres;
    }

    function displayMembers($tri, $page, $recherche){
        $membres = getMembers();
        $membresFiltres = array();
        $realMembers = array();
        $nicknames = array();
        $discriminators = array();
        
        foreach ($membres as $membre){
            if ((isset($membre->user->bot) === false) && verificationModeration($membre) === false) {
                $membresFiltres[] = $membre;
                $nicknames[] = $membre->user->username;
                $discriminators[] = $membre->user->discriminator;
            }
        }

        $nbMembresReels = count($membresFiltres);
        $nbMembresParPage = 10;
        $nbPages = ceil($nbMembresReels / $nbMembresParPage);

        if ($page > $nbPages || $page < 1) {
            $page = 1;
        }

        if ($nbPages == 0) {
            $nbPages = 1;
        }

        $debut = ($page - 1) * $nbMembresParPage;

        switch ($tri) {
            case 'pseudoAZ':
                natcasesort($nicknames);
                $nicknames = array_values($nicknames);
                $tableauDeTri = $nicknames;
                $nomCurrentTri = "De A à Z";
                break;

            case 'pseudoZA':
                natcasesort($nicknames);
                $nicknames = array_values($nicknames);
                $nicknames = array_reverse($nicknames);
                $tableauDeTri = $nicknames;
                $nomCurrentTri = "De Z à A";
                break;

            case 'discrimCroi':
                sort($discriminators, SORT_NUMERIC);
                $discriminators = array_values($discriminators);
                $tableauDeTri = $discriminators;
                $nomCurrentTri = "Discriminateur croissant";
                break;

            case 'discrimDecroi':
                sort($discriminators, SORT_NUMERIC);
                $discriminators = array_values($discriminators);
                $discriminators = array_reverse($discriminators);
                $tableauDeTri = $discriminators;
                $nomCurrentTri = "Discriminateur décroissant";
                break;
        }
        
        $tableauDeTri = array_slice($tableauDeTri, $debut, $nbMembresParPage);
        $compteur = 0;
        
        foreach ($tableauDeTri as $valeur){
            if ($compteur === $nbMembresParPage) {
                break;
            }

            foreach ($membresFiltres as $membreFiltre){
                switch ($tri) {
                    case 'pseudoAZ':
                        $tabKey = $membreFiltre->user->username;
                        break;
        
                    case 'pseudoZA':
                        $tabKey = $membreFiltre->user->username;
                        break;
        
                    case 'discrimCroi':
                        $tabKey = $membreFiltre->user->discriminator;
                        break;
        
                    case 'discrimDecroi':
                        $tabKey = $membreFiltre->user->discriminator;
                        break;
                }
    
                if ($tabKey === $valeur) {
                    $realMembers[] = $membreFiltre;
                    $compteur++;
                }
                
            }
        }

        require 'src/view/moderation/membresView.php';
    }

    //Obtenir tous les membres bannis du serveur Discord.
    function getBannedMembers(){
        $apiURLBanned = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/bans?limit=1000';

        $membresBannis = apiRequest2($apiURLBanned);

        return $membresBannis;
    }

    function displayBannedMembers($tri, $page, $recherche){
        verifierMembresBannis();
        $bannedMembers = getBannedMembers();
        $membresBannis = array();
        $nicknames = array();
        $discriminators = array();

        foreach ($bannedMembers as $bannedMember){
            if ((isset($bannedMember->user->bot) === false)) {
                $nicknames[] = $bannedMember->user->username;
                $discriminators[] = $bannedMember->user->discriminator;
            }
        }

        $nbMembresBannis = count($bannedMembers);
        $nbMembresParPage = 10;
        $nbPages = ceil($nbMembresBannis / $nbMembresParPage);

        if ($page > $nbPages || $page < 1) {
            $page = 1;
        }

        if ($nbPages == 0) {
            $nbPages = 1;
        }

        $debut = ($page - 1) * $nbMembresParPage;
        
        switch ($tri) {
            case 'pseudoAZ':
                natcasesort($nicknames);
                $nicknames = array_values($nicknames);
                $tableauDeTri = $nicknames;
                $nomCurrentTri = "De A à Z";
                break;

            case 'pseudoZA':
                natcasesort($nicknames);
                $nicknames = array_values($nicknames);
                $nicknames = array_reverse($nicknames);
                $tableauDeTri = $nicknames;
                $nomCurrentTri = "De Z à A";
                break;

            case 'discrimCroi':
                sort($discriminators, SORT_NUMERIC);
                $discriminators = array_values($discriminators);
                $tableauDeTri = $discriminators;
                $nomCurrentTri = "Discriminateur croissant";
                break;

            case 'discrimDecroi':
                sort($discriminators, SORT_NUMERIC);
                $discriminators = array_values($discriminators);
                $discriminators = array_reverse($discriminators);
                $tableauDeTri = $discriminators;
                $nomCurrentTri = "Discriminateur décroissant";
                break;
        }

        $tableauDeTri = array_slice($tableauDeTri, $debut, $nbMembresParPage);
        $compteur = 0;

        foreach ($tableauDeTri as $valeur){
            if ($compteur === $nbMembresParPage) {
                break;
            }

            foreach ($bannedMembers as $bannedMember){
                switch ($tri) {
                    case 'pseudoAZ':
                        $tabKey = $bannedMember->user->username;
                        break;
        
                    case 'pseudoZA':
                        $tabKey = $bannedMember->user->username;
                        break;
        
                    case 'discrimCroi':
                        $tabKey = $bannedMember->user->discriminator;
                        break;
        
                    case 'discrimDecroi':
                        $tabKey = $bannedMember->user->discriminator;
                        break;
                }
    
                if ($tabKey === $valeur) {
                    $membresBannis[] = $bannedMember;
                    $compteur++;
                }
                
            }
        }

        require 'src/view/moderation/membresBannisView.php';
    }

    //Vérifie les membres bannis obtenus par l'API. S'ils sont inexistants dans la base de données, ils sont ajoutés automatiquement.
    function verifierMembresBannis(){
        $membresBannis = getBannedMembers();
        $listeSanctions = getAllSanctions();
        //Tableau des id Discord recueillis du tableau $listeSanctions
        $idDiscSanc = array();
        //Date pour l'éventuelle création d'une sanction dans la BDD
        $date = new DateTime();
        $dateBDD = $date->format('Y-m-d H:i:s');

        foreach ($listeSanctions as $sanction) {
            $idDiscSanc[] = $sanction->getIdDiscord();
        }

        foreach ($membresBannis as $membreBanni){
            $idDiscord = $membreBanni->user->id;
            $typeSanction = "Bannissement";
            $raison = $membreBanni->reason;
            if (array_search($idDiscord, $idDiscSanc) === false) {
                addSanction($idDiscord, $typeSanction, $raison, $dateBDD);
            }
        }
    }

    //Obtenir un membre banni en fonction de son identifiant
    function getBannedMemberByDiscordId($id){
        $membresBannis = getBannedMembers();
        $bannedMember = false;

        foreach ($membresBannis as $membreBanni) {
            if ($membreBanni->user->id === $id) {
                $bannedMember = $membreBanni;
                break;
            }
        }

        return $bannedMember;
    }

    //Voir la raison pour laquelle un membre a été banni.
    function viewRaison(){
        verifierMembresBannis();

        $id = $_GET["id"];

        $user = getBannedMemberByDiscordId($id);

        $sanction = getUserLastBan($id);
        
        require_once 'src/view/moderation/dernierBanUtilView.php';
    }

    
    //Afficher l'historique des membres
    function displayLastestSanctions(
        $tri, 
        $filtre, 
        $page, 
        $recherche, 
        $valBan,
        $valExpu, 
        $valAvert
    )
    {
        // Vérifier et actualiser les données des utilisateurs Discord sanctionnés sur le serveur.
        verifierUtilisateursSanctionnes();

        //-----------
        // Recherche
        //-----------
       

        //---------
        // Filtres
        //---------

        // Initialisation de la variable $sqlFiltre pour les requêtes SQL
        $sqlFiltre = "";

        switch ($filtre) {
            case 'all':
                $sqlFiltre = "";
                $valBan = 1;
                $valExpu = 1;
                $valAvert = 1;
                break;

            case 'banOnly':
                $sqlFiltre = "WHERE sanction.typeSanction='Bannissement'";
                $valBan = 1;
                $valExpu = 0;
                $valAvert = 0;
                break;

            case 'expuOnly':
                $sqlFiltre = "WHERE sanction.typeSanction='Expulsion'";
                $valBan = 0;
                $valExpu = 1;
                $valAvert = 0;
                break;

            case 'avertOnly':
                $sqlFiltre = "WHERE sanction.typeSanction='Avertissement'";
                $valBan = 0;
                $valExpu = 0;
                $valAvert = 1;
                break;

            case 'banExpu':
                $sqlFiltre = "WHERE sanction.typeSanction IN ('Bannissement', 'Expulsion')";
                $valBan = 1;
                $valExpu = 1;
                $valAvert = 0;
                break;

            case 'banAvert':
                $sqlFiltre = "WHERE sanction.typeSanction IN ('Bannissement', 'Avertissement')";
                $valBan = 1;
                $valExpu = 0;
                $valAvert = 1;
                break;
            
            case 'expuAvert':
                $sqlFiltre = "WHERE sanction.typeSanction IN ('Expulsion', 'Avertissement')";
                $valBan = 0;
                $valExpu = 1;
                $valAvert = 1;
                break;
            
        }

        //-----
        // Tri
        //-----

        // Initialisation de la variable $sqlTri pour les requêtes SQL
        $sqlTri = "";

        switch ($tri) {
            case 'lastASC':
                $sqlTri = "ORDER BY sanction.id ASC";
                $nomCurrentTri = "Chronologique";
                break;
                
            case 'lastDESC':
                $sqlTri = "ORDER BY sanction.id DESC";
                $nomCurrentTri = "Antéchronologique";
                break;
            
            case 'pseudoASC':
                $sqlTri = "ORDER BY utilisateursanctionne.username ASC";
                $nomCurrentTri = "De A à Z";
                break;
                
            case 'pseudoDESC':
                $sqlTri = "ORDER BY utilisateursanctionne.username DESC";
                $nomCurrentTri = "De Z à A";
                break;

            case 'discrimASC':
                $sqlTri = "ORDER BY utilisateursanctionne.discriminator ASC";
                $nomCurrentTri = "Discriminateur croissant";
                break;

            case 'discrimDESC':
                $sqlTri = "ORDER BY utilisateursanctionne.discriminator DESC";
                $nomCurrentTri = "Discriminateur décroissant";
                break;
        }
        
        //------------------------
        // Pagination + sanctions
        //------------------------
        $sql = "SELECT COUNT(id) FROM sanction " . $sqlFiltre;
        $nbSanctions = requeteInfoSanctionSQL($sql); //Nombre total de sanctions
        $nbSanctionsParPage = 10; //Nombre de sanctions par page
        $nbPages = ceil($nbSanctions / $nbSanctionsParPage);

        if ($page > $nbPages || $page < 1) {
            $page = 1;
        }

        if ($nbPages == 0) {
            $nbPages = 1;
        }

        $debut = ($page - 1) * $nbSanctionsParPage;
        $sqlParam = "INNER JOIN utilisateursanctionne ON sanction.idDiscord = utilisateursanctionne.idDiscord";
        $sqlParam .= " " . $sqlFiltre . " " . $sqlTri . " ";

        $sanctions = getSpecificSanctions($sqlParam, $debut, $nbSanctionsParPage);

        if ($sanctions === false) {
            $sanctions = "Aucune sanction trouvée";
        }

        //Tableau des utilisateurs sanctionnés
        $utilisateursS = getAllUtilisateursSanctionnes();

        require "src/view/moderation/listeSanctionsView.php";
    }
?>