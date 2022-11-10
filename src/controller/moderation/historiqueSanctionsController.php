<?php 
    require_once SITE_ROOT . "/src/controller/moderation/utilisateurSanctionneController.php";
    require_once SITE_ROOT . "/src/services/moderation/donneesModerationService.php";

    function displayLastestSanctions(
        $tri, 
        $filtre, 
        $page, 
        $recherche
    )
    {
        // Initialisation pour affichage
        $termeDeRecherche = "";
        // Menu Modération couleur
        $menuMod = 'hs';
        // Tableau servant à la recherche par information, récoltant les infos des utilisateurs.
        $infosUsers = [];

        $sanctionsAAfficher = [];

        $sanctions = getAllSanctions();
        $utilisateursS = getAllUtilisateursSanctionnes();

        $usernames = getUSUsernames($utilisateursS);
        $discriminators = getUSDiscriminators($utilisateursS);
        $identifiantsSanctions = getIdSanctions($sanctions);
        $infosUsers = getInfoUS($utilisateursS);

        //-----------
        // Recherche
        //-----------
        if ($recherche !== false) {
            $termeDeRecherche = htmlspecialchars($recherche);
            $utilisateursCorrespondants = rechercheMembresCorrespondants($infosUsers, $termeDeRecherche);

            if ($utilisateursCorrespondants !== false) {
                $nouveauxUS = convertirEnUS($utilisateursS, $utilisateursCorrespondants);
                $usernames = getUSUsernames($nouveauxUS);
                $discriminators = getUSDiscriminators($nouveauxUS);
                $utilisateursS = $nouveauxUS;
            } else {
                $utilisateursS = false;
            }
        } else {
            if (!isset($termeDeRecherche)) {
                $termeDeRecherche = false;
            }
        }

        //---------
        // Filtres
        //---------

        // Initialisation de la variable $sqlFiltre pour les requêtes SQL
        $sqlFiltre = "";

        switch ($filtre) {
            case 'all':
                $sanctionsFiltrees = $sanctions;
                $valBan = 1;
                $valExpu = 1;
                $valAvert = 1;
                break;

            case 'banOnly':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, true, false, false);
                $valBan = 1;
                $valExpu = 0;
                $valAvert = 0;
                break;

            case 'expuOnly':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, false, true, false);
                $valBan = 0;
                $valExpu = 1;
                $valAvert = 0;
                break;

            case 'avertOnly':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, false, false, true);
                $valBan = 0;
                $valExpu = 0;
                $valAvert = 1;
                break;

            case 'banExpu':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, true, true, false);
                $valBan = 1;
                $valExpu = 1;
                $valAvert = 0;
                break;

            case 'banAvert':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, true, false, true);;
                $valBan = 1;
                $valExpu = 0;
                $valAvert = 1;
                break;
            
            case 'expuAvert':
                $sanctionsFiltrees = filtrerLesSanctions($sanctions, false, true, true);
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
                $tableauATrier = $identifiantsSanctions;
                $tableauDeTri = triSanctions($tableauATrier, false);
                $nomCurrentTri = "Chronologique";
                break;
                
            case 'lastDESC':
                $tableauATrier = $identifiantsSanctions;
                $tableauDeTri = triSanctions($tableauATrier, true);
                $nomCurrentTri = "Antéchronologique";
                break;
            
            case 'pseudoASC':
                $tableauATrier = $usernames;
                $tableauDeTri = triUtilisateurs($tableauATrier, 0, count($tableauATrier), false, false);
                $nomCurrentTri = "De A à Z";
                break;
                
            case 'pseudoDESC':
                $tableauATrier = $usernames;
                $tableauDeTri = triUtilisateurs($tableauATrier, 0, count($tableauATrier), false, true);
                $nomCurrentTri = "De Z à A";
                break;

            case 'discrimASC':
                $tableauATrier = $discriminators;
                $tableauDeTri = triUtilisateurs($tableauATrier, 0, count($tableauATrier), true, false);
                $nomCurrentTri = "Discriminateur croissant";
                break;

            case 'discrimDESC':
                $tableauATrier = $discriminators;
                $tableauDeTri = triUtilisateurs($tableauATrier, 0, count($tableauATrier), true, true);
                $nomCurrentTri = "Discriminateur décroissant";
                break;
        }

        if ($utilisateursS === false || $sanctionsFiltrees === false) {
            $sanctionsAAfficher = false;
            $nbPages = 1;
        } else {
            if ($tri == 'lastASC' || $tri == 'lastDESC') {
                $sanctionsComposees = composerSanctions($tableauDeTri, $sanctionsFiltrees, $sanctions, $utilisateursS, true);
            } else {
                $sanctionsComposees = composerSanctions($tableauDeTri, $sanctionsFiltrees, $sanctions, $utilisateursS, false);
            }

            //------------
            // Pagination 
            //------------
            $nbSanctionsParPage = NB_MEMBRES_AFFICHAGE_MOD;
            $nbPages = obtenirNbPages($sanctionsComposees, $nbSanctionsParPage);
            
            $debut = ($page - 1) * $nbSanctionsParPage;
            $sanctionsAAfficher = array_slice($sanctionsComposees, $debut, $nbSanctionsParPage);
        }

        //------------
        // Pagination 
        //------------
        if ($page > $nbPages || $page < 1) {
            $page = 1;
        }

        require "src/view/moderation/listeSanctionsView.php";
    }

    function filtrerLesSanctions(
        $sanctions, 
        $ban, 
        $expu, 
        $avert
    )
    {
        $sanctionsFiltrees = [];

        foreach ($sanctions as $sanction) {
            switch ($sanction->getTypeSanction()) {
                case 'Bannissement':
                    if ($ban === true) {
                        $sanctionsFiltrees[] = $sanction;
                    }
                    break;
                
                case 'Expulsion':
                    if ($expu === true) {
                        $sanctionsFiltrees[] = $sanction;
                    }
                    break;

                case 'Avertissement':
                    if ($avert === true) {
                        $sanctionsFiltrees[] = $sanction;
                    }
                    break;
            }
        }

        if (empty($sanctionsFiltrees) === true) {
            return false;
        }

        return $sanctionsFiltrees;
    }

    //-----------------------------------
    // INFO :
    // US = Utilisateurs Sanctionnés
    //-----------------------------------
    function getUSUsernames($users){
        $usernames = [];

        foreach ($users as $user) {
            $usernames[] = [
                'username' => $user->getUsername(),
                'userId' => $user->getIdDiscord()
            ];
        }

        return $usernames;
    }

    function getUSDiscriminators($users){
        $discriminators = [];

        foreach ($users as $user) {
            $discriminators[] = [
                'discriminator' => $user->getDiscriminator(),
                'userId' => $user->getIdDiscord()
            ];
        }

        return $discriminators;
    }

    function getIdSanctions($sanctions){
        $identifiants = [];

        foreach ($sanctions as $sanction){
            $identifiants[] = [
                'identifiant' => $sanction->getId(),
                'userId' => $sanction->getIdDiscord()
            ];
        }

        return $identifiants;
    }

    function getInfoUS($users){
        $infosUsers = [];

        foreach ($users as $user) {
            $infosUsers[] = [
                'idDiscord' => $user->getIdDiscord(),
                'pseudonyme' => $user->getUsername(),
                'discriminator' => $user->getDiscriminator(),
                'surnom' => $user->getSurnom()
            ];
        }

        return $infosUsers;
    }

    function convertirEnUS($utilisateursS, $utilisateursCorrespondants){
        $utilisateursConvertis = [];

        foreach($utilisateursS as $us){
            foreach ($utilisateursCorrespondants as $utilCorresp) {
                if ($us->getIdDiscord() == $utilCorresp['idDiscord']) {
                    $utilisateursConvertis[] = $us;
                }
            }
        }

        return $utilisateursConvertis;
    }

    function triSanctions($tabSanctions, $inverser) :array 
    {
        array_multisort(array_column($tabSanctions, 'identifiant'), SORT_NUMERIC, $tabSanctions);

        if ($inverser === true) {
            $tabSanctions = array_reverse($tabSanctions);
        }

        $tableauDeTri = $tabSanctions;

        return $tableauDeTri;
    }

    function composerSanctions(array $tableauDeTri, array $sanctionsFiltrees, array $sanctions, array $utilSanc, bool $typeTriSanction): array
    {
        $sanctionsComposees = [];

        if ($typeTriSanction === true) {
            $sousTableau = $sanctionsFiltrees;
        } else {
            $sousTableau = $utilSanc;
        }

        if ($typeTriSanction === true) {
            foreach ($tableauDeTri as $valeurDuTableau) {
                foreach($utilSanc as $us){
                    if ($us->getIdDiscord() == $valeurDuTableau['userId']) {
                        $sanctionsComposees[] = convertirEnSanction($sanctions, $valeurDuTableau);
                    }
                }
            }
        } else {
            foreach($tableauDeTri as $valeurDuTableau){
                foreach($sanctionsFiltrees as $sanctionF){
                    if ($valeurDuTableau['userId'] == $sanctionF->getIdDiscord()) {
                        $sanctionsComposees[] = $sanctionF;
                    }
                }
            }
        }

        return $sanctionsComposees;
    }

    function convertirEnSanction(array $tabSanctions, array $sanctionAConvertir){
        $sanctionConvertie = "";

        foreach ($tabSanctions as $sanction){
            if ($sanctionAConvertir['identifiant'] == $sanction->getId()){
                $sanctionConvertie = $sanction;
            }
        }

        return $sanctionConvertie;
    }

?>