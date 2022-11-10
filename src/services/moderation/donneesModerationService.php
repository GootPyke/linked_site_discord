<?php 
    require_once SITE_ROOT . '/src/controller/autres/connexionController.php';
    require_once SITE_ROOT . "/src/model/moderation/sanctionsModel.php";
    
    //Fonction permettant de définir les données nécessaires à la modération
    function getDataForMod(){
        //Données sur les membres du serveur.
        $apiURLMembers = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/members?limit=1000';
        $_SESSION["membres"] = apiRequest2($apiURLMembers);

        //Données sur les membres bannis du serveur.
        $apiURLBanned = API_REFERENCE . 'guilds/'. ID_SERVEUR .'/bans?limit=1000';
        $_SESSION["membresBannis"] = apiRequest2($apiURLBanned);
    }

    //Obtenir un membre banni en fonction de son identifiant
    function getBannedMemberByDiscordId($id){
        $membresBannis = $_SESSION["membresBannis"];
        $bannedMember = false;

        foreach ($membresBannis as $membreBanni) {
            if ($membreBanni->user->id === $id) {
                $bannedMember = $membreBanni;
                break;
            }
        }

        return $bannedMember;
    }

    //Obtenir un membre banni en fonction de son identifiant
    function getMemberByDiscordId($id)
    {
        $membres = $_SESSION["membres"];
        $membreARetourner = false;

        foreach ($membres as $membre) {
            if ($membre->user->id === $id) {
                $membreARetourner = $membre;
                break;
            }
        }

        return $membreARetourner;
    }

    //Voir la raison pour laquelle un membre a été banni.
    function voirRaison($idSanction){
        $sanction = getSanctionById($idSanction);
        $user = getUtilisateurSanctionneByIdDiscord($sanction->getIdDiscord());
        
        require_once SITE_ROOT. '/src/view/moderation/voirRaisonView.php';
    }

    //Vérifie les membres bannis obtenus par l'API. S'ils sont inexistants dans la base de données, ils sont ajoutés automatiquement.
    function verifierMembresBannis(){
        $membresBannis = $_SESSION["membresBannis"];
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

    function triUtilisateurs($tableauATrier, $debut, $nbMembresParPage, $typeTriNumerique = false, $inverser = false): array
    {
        $flagTri = SORT_NATURAL | SORT_FLAG_CASE;
        $cleDeTri = 'username';
        
        if ($typeTriNumerique) {
            $flagTri = SORT_NUMERIC;
            $cleDeTri = 'discriminator';
        }

        array_multisort(array_column($tableauATrier, $cleDeTri), $flagTri, $tableauATrier);

        if ($inverser === true) {
            $tableauATrier = array_reverse($tableauATrier);
        }

        $tableauDeTri = $tableauATrier;
        $tableauDeTri = array_slice($tableauDeTri, $debut, $nbMembresParPage);

        return $tableauDeTri;
    }
    
    function rechercheMembresCorrespondants($users, $termeDeRecherche)
    {
        $membresCorrespondantsALaRecherche = [];

        foreach ($users as $user) {
            $pseudoEtDiscriminateur = $user['pseudonyme']."#".$user['discriminator'];
            $pseudo = $user['pseudonyme'];
            $discriminator = $user['discriminator'];
            $userId = $user['idDiscord'];

            if ($userId === $termeDeRecherche) {
                $membresCorrespondantsALaRecherche[] = $user;
                break;
            }

            if (array_search($termeDeRecherche, $user) !== false) {
                $membresCorrespondantsALaRecherche[] = $user;
            } else if (stripos($pseudoEtDiscriminateur, (string) $termeDeRecherche) !== false) {
                $membresCorrespondantsALaRecherche[] = $user;
            }
        }

        if (empty($membresCorrespondantsALaRecherche) === true) {
            return false;
        }

        return $membresCorrespondantsALaRecherche;
    }

    function filtrerLesMembresDeLaModeration($users){
        $utilisateursFiltres = [];

        foreach ($users as $user) {
            if(
                (isset($user->user->bot) === false) 
                && (verificationModeration($user) === false)
            )
            {
                $utilisateursFiltres[] = $user;
            }
        }

        return $utilisateursFiltres;
    }

    function getUsernames($users){
        $usernames = [];

        foreach ($users as $user) {
            $usernames[] = [
                'username' => $user->user->username, 
                'userId' => $user->user->id
            ];
        }

        return $usernames;
    }

    function getDiscriminators($users){
        $discriminators = [];

        foreach ($users as $user) {
            $discriminators[] = [
                'discriminator' => $user->user->discriminator, 
                'userId' => $user->user->id
            ];
        }

        return $discriminators;
    }

    function getInfoUsers($users){
        $infosUsers = [];

        foreach ($users as $user) {
            if (isset($user->nick)) {
                $nick = $user->nick;
            } else {
                $nick = false;
            }

            $infosUsers[] = [
                'idDiscord' => $user->user->id,
                'pseudonyme' => $user->user->username,
                'discriminator' => $user->user->discriminator,
                'surnom' => $nick
            ];
        }

        return $infosUsers;
    }

    // Cette fonction retourne la classe de l'utilisateur que Discord donne.
    function convertirEnUtilisateursDiscord($users, $utilisateursCorrespondants){
        $utilisateursConvertis = [];

        foreach($users as $user){
            foreach($utilisateursCorrespondants as $utilCorresp){
                if ($user->user->id == $utilCorresp['idDiscord']){
                    $utilisateursConvertis[] = $user;
                } 
            }
        }

        return $utilisateursConvertis;
    }
?>