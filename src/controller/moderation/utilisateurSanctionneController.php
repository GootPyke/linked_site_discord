<?php 
    require_once SITE_ROOT . "/src/model/moderation/utilisateurSanctionneModel.php";
    require_once SITE_ROOT . "/src/model/moderation/sanctionsModel.php";

    //Fonction qui va actualiser les utilisateurs sanctionnés en ajoutant les membres bannis non référencés dans la base de données
    function addNotReferencedBannedUsers(){
        // Tableau des membres bannis (via API Discord)
        $bannedMembers = $_SESSION["membresBannis"];
        // Tableau des utilisateurs sanctionnés
        $listUserSanction = getAllUtilisateursSanctionnes();
        //Tableau des identifiants des utilisateurs sanctionnés
        $tabIdUserSanc = array();

        //Parcourir les utilisateurs sanctionnés et ajouter leurs identifiants Discord dans le tableau $tabIdUserSanc
        foreach ($listUserSanction as $userSanction) {
            $tabIdUserSanc[] = $userSanction->getIdDiscord();
        }

        //Parcourir les membres bannis et l'un d'eux n'est pas dans la BDD, l'ajouter
        foreach ($bannedMembers as $membreBanni){
            $idDiscord = $membreBanni->user->id;
            $username = $membreBanni->user->username;
            $discriminator = $membreBanni->user->discriminator;
            $avatar = $membreBanni->user->avatar;
            if (array_search($idDiscord, $tabIdUserSanc) === false) {
                addUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar);
            }
        }
    }

    //Fonction qui va ajouter un utilisateur sanctionné après vérification qu'il ne soit pas déjà dans la base de données
    function addToDataUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar, $surnom = NULL){
        // Tableau des utilisateurs sanctionnés
        $listUserSanction = getAllUtilisateursSanctionnes();
        //Tableau des identifiants des utilisateurs sanctionnés
        $tabIdUserSanc = array();

        //Parcourir les utilisateurs sanctionnés et ajouter leurs identifiants Discord dans le tableau $tabIdUserSanc
        foreach ($listUserSanction as $userSanction) {
            $tabIdUserSanc[] = $userSanction->getIdDiscord();
        }

        if (array_search($idDiscord, $tabIdUserSanc) === false) {
            addUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatar, $surnom);
        }
    }

    //Fonction qui va actualiser les informations des utilisateurs sanctionnés du serveur
    function updateMembresSanctionnes(){
        //Tableau des membres du serveur
        $listMembres = $_SESSION["membres"];
        //Tableau des membres bannis
        $bannedMembers = $_SESSION["membresBannis"];    
        //Tableau des utilisateurs sanctionnés
        $listUserSanction = getAllUtilisateursSanctionnes();

        foreach ($bannedMembers as $membreBanni) {
            $listMembres[] = $membreBanni;
        }

        //On parcourt les membres du serveur Discord puis on parcourt les utilisateurs sanctionnés
        foreach ($listMembres as $membre){
            foreach ($listUserSanction as $userSanction) {
                // Si un id match, alors on vérifie si ses informations sont toujours les mêmes dans la BDD
                if ($membre->user->id === $userSanction->getIdDiscord()) {
                    
                    if ($userSanction->getUsername() !== htmlspecialchars($membre->user->username)) {
                        editUsername($membre->user->id, htmlspecialchars($membre->user->username));
                    }

                    if ($userSanction->getDiscriminator() !== $membre->user->discriminator) {
                        editDiscriminator($membre->user->id, $membre->user->discriminator);
                    }

                    if ($userSanction->getAvatarHash() !== $membre->user->avatar) {
                        editAvatarHash($membre->user->id, $membre->user->avatar);
                    }

                    if (isset($membre->nick)) {
                        if ($userSanction->getSurnom() !== $membre->nick) {
                            editSurnom($membre->user->id, $membre->nick);
                        }
                    }
                }
            }
        }
    }

    function verifierUtilisateursSanctionnes(){
        addNotReferencedBannedUsers();
        updateMembresSanctionnes();
    }
?>