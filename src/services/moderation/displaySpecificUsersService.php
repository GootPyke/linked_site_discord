<?php 
    require_once SITE_ROOT . '/src/services/moderation/donneesModerationService.php';
    require_once SITE_ROOT . '/src/helper/paginationHelper.php';
    require_once SITE_ROOT . '/src/model/moderation/sanctionsModel.php';

    function displaySpecificUsers(
        $tri,
        $page,
        $recherche,
        $bannedUsers = false
    )
    {
        $users = $_SESSION["membresBannis"];
        // Utilisateurs après tri. 
        $utilisateursFinaux = [];
        // Utilisateurs à afficher en fonction du nombre d'utilisateurs par page.
        $utilisateursAAfficher = [];
        // Tableaux de tri
        $usernames = [];
        $discriminators = [];
        // Tableau servant à la recherche par information, récoltant les infos des utilisateurs.
        $infosUsers = [];
        //Pour allumer l'ancre dans le menu sur la gauche 
        $menuMod = 'mb'; // Utilisateurs Bannis
        
        $utilisateursFinaux = $users;

        if (!$bannedUsers) {
            $menuMod = 'mm'; // Membres du serveur
            $users = $_SESSION["membres"];
            $utilisateursFinaux = filtrerLesMembresDeLaModeration($users);
        }

        $usernames = getUsernames($utilisateursFinaux);
        $discriminators = getDiscriminators($utilisateursFinaux);
        $infosUsers = getInfoUsers($utilisateursFinaux);

        if ($recherche !== false) {
            $termeDeRecherche = htmlspecialchars($recherche);
            $utilisateursCorrespondants = rechercheMembresCorrespondants($infosUsers, $termeDeRecherche);

            if ($utilisateursCorrespondants !== false) {
                $nouveauxUtilisateurs = convertirEnUtilisateursDiscord($utilisateursFinaux, $utilisateursCorrespondants);
                
                $usernames = getUsernames($nouveauxUtilisateurs);

                $discriminators = getDiscriminators($nouveauxUtilisateurs);
    
                $utilisateursFinaux = $nouveauxUtilisateurs;
                
            } else {
                $utilisateursFinaux = false;
            }
        } else {
            if (!isset($termeDeRecherche)) {
                $termeDeRecherche = false;
            }
        }

        $usersIdForSanctions = [];

        foreach ($utilisateursFinaux as $utilisateur) {
            $usersIdForSanctions[] = $utilisateur->user->id;
        }

        $sanctions = getUserSanctionsByIdDiscord($usersIdForSanctions);

        foreach ($utilisateursFinaux as &$utilisateur) {
            $utilisateur->sanctions = [];

            foreach ($sanctions as $sanction){
                if ($utilisateur->user->id === $sanction->getIdDiscord()) {
                    if ($sanction->getTypeSanction() === 'Bannissement') {
                        $utilisateur->sanctions['bannissement'][] = $sanction;
                        continue;
                    }

                    if ($sanction->getTypeSanction() === 'Expulsion') {
                        $utilisateur->sanctions['expulsion'][] = $sanction;
                        continue;
                    }

                    if ($sanction->getTypeSanction() === 'Avertissement') {
                        $utilisateur->sanctions['avertissement'][] = $sanction;
                    }
                    
                }
            }
        }

        $nbUtilisateursParPage = NB_MEMBRES_AFFICHAGE_MOD;

        if ($utilisateursFinaux === false) {
            $nbPages = 1;
        } else {
            $nbPages = obtenirNbPages($utilisateursFinaux, $nbUtilisateursParPage);
        }

        if ($page > $nbPages || $page < 1) {
            $page = 1;
        }

        if ($nbPages == 0) {
            $nbPages = 1;
        }

        if ($utilisateursFinaux !== false) {
            $debut = ($page - 1) * $nbUtilisateursParPage;
    
            switch ($tri) {
                case 'pseudoAZ':
                    $tableauATrier = $usernames;
                    $tableauDeTri = triUtilisateurs($tableauATrier, $debut, $nbUtilisateursParPage, false, false);
                    $nomCurrentTri = "De A à Z";
    
                    break;
    
                case 'pseudoZA':
                    $tableauATrier = $usernames;
                    $tableauDeTri = triUtilisateurs($tableauATrier, $debut, $nbUtilisateursParPage, false, true);
                    $nomCurrentTri = "De Z à A";
    
                    break;
    
                case 'discrimCroi':
                    $tableauATrier = $discriminators;
                    $tableauDeTri = triUtilisateurs($tableauATrier, $debut, $nbUtilisateursParPage, true, false);
                    $nomCurrentTri = "Discriminateur croissant";
    
                    break;
    
                case 'discrimDecroi':
                    $tableauATrier = $discriminators;
                    $tableauDeTri = triUtilisateurs($tableauATrier, $debut, $nbUtilisateursParPage, true, true);
                    $nomCurrentTri = "Discriminateur décroissant";
    
                    break;
            }
    
            foreach ($tableauDeTri as $valeurTabTri) {
                foreach ($utilisateursFinaux as $utilisateurFinal) {
                    if ($tri === "discrimCroi" || $tri === "discrimDecroi") {
                        $tabKey = $utilisateurFinal->user->discriminator;
                        $keyTabTri = 'discriminator';
                    } else {
                        if ($tri === "pseudoAZ" || $tri === "pseudoZA") {
                            $tabKey = $utilisateurFinal->user->username;
                            $keyTabTri = 'username';
                        }
                    }
    
                    $userId = $utilisateurFinal->user->id;
    
                    if ($valeurTabTri[$keyTabTri] === $tabKey && $valeurTabTri['userId'] === $userId) {
                        $utilisateursAAfficher[] = $utilisateurFinal;
                    }
                }
            }
        } else {
            $utilisateursAAfficher = "Aucun résultat trouvé";
            $nomCurrentTri = "De A à Z";
        }

        if (!$bannedUsers){
            require_once SITE_ROOT . '/src/view/moderation/membresView.php';
        } else {
            require_once SITE_ROOT . '/src/view/moderation/membresBannisView.php';
        }
    }
?>