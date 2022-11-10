<?php 
        require_once SITE_ROOT . "/src/model/autres/connexionBDDModel.php";
    
    class UtilisateurSanctionne{
        private $id;
        private $idDiscord;
        private $username;
        private $discriminator;
        private $avatarHash;
        private $surnom;
        
        public function getId() {return $this->id;}
        
        public function getIdDiscord() {return $this->idDiscord;}
        public function setIdDiscord($idDiscord) {$this->idDiscord = $idDiscord;}

        public function getUsername() {return $this->username;}
        public function setUsername($username) {$this->username = $username;}
        
        public function getDiscriminator() {return $this->discriminator;}
        public function setDiscriminator($discriminator) {$this->discriminator = $discriminator;}
        
        public function getAvatarHash() {return $this->avatarHash;}
        public function setAvatarHash($avatarHash) {$this->avatarHash = $avatarHash;}

        public function getSurnom() {return $this->surnom;}
        public function setSurnom($surnom) {$this->surnom = $surnom;}
    }
    
    //Obtenir toutes les sanctions pour les afficher
    function getAllUtilisateursSanctionnes(){
        $sql = "SELECT * FROM utilisateursanctionne";
        $data = [];
        
        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);
            
            $req->setFetchMode(PDO::FETCH_CLASS, 'UtilisateurSanctionne');
            
            $req->execute();
            
            $data = $req->fetchAll();
        } catch (PDOException $ex) {
            return false;
        } finally {
            return $data;
        }
    }

    function getUtilisateurSanctionneByIdDiscord($idDiscord){
        $sql = "SELECT * FROM utilisateursanctionne WHERE idDiscord= :idDiscord";
        $data = "";
        
        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            
            $req->setFetchMode(PDO::FETCH_CLASS, 'UtilisateurSanctionne');
            
            $req->execute();
            
            $data = $req->fetch();
        } catch (PDOException $ex) {
            return false;
        } finally {
            return $data;
        }
    }
    
    function addUtilisateurSanctionne($idDiscord, $username, $discriminator, $avatarHash, $surnom = NULL) {
        $sql = "INSERT INTO utilisateursanctionne(idDiscord, username, discriminator, avatarHash, surnom) VALUES (:idDiscord, :username, :discriminator, :avatarHash, :surnom)";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':username', $username, PDO::PARAM_STR);
            $req->bindValue(':discriminator', $discriminator, PDO::PARAM_STR);
            $req->bindValue(':avatarHash', $avatarHash, PDO::PARAM_STR);
            $req->bindValue(':surnom', $surnom, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de l'ajout d'un utilisateur sanctionné : {$ex->getMessage()}");
        }
    }

    function editUsername($idDiscord, $newUsername){
        $sql = "UPDATE utilisateursanctionne SET username= :newUsername WHERE idDiscord= :idDiscord";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':newUsername', $newUsername, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la modification du pseudonyme d'un utilisateur : {$ex->getMessage()}");
        }
    }

    function editDiscriminator($idDiscord, $newDiscriminator){
        $sql = "UPDATE utilisateursanctionne SET discriminator= :newDiscriminator WHERE idDiscord= :idDiscord";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':newDiscriminator', $newDiscriminator, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la modification du discriminateur d'un utilisateur : {$ex->getMessage()}");
        }
    }

    function editAvatarHash($idDiscord, $newAvatarHash){
        $sql = "UPDATE utilisateursanctionne SET avatarHash= :newAvatarHash WHERE idDiscord= :idDiscord";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':newAvatarHash', $newAvatarHash, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la modification du Hash Avatar d'un utilisateur : {$ex->getMessage()}");
        }
    }

    function editSurnom($idDiscord, $newSurnom){
        $sql = "UPDATE utilisateursanctionne SET surnom= :newSurnom WHERE idDiscord= :idDiscord";

        try {
            $bdd = connexionBDD();

        $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':newSurnom', $newSurnom, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la modification du surnom d'un utilisateur : {$ex->getMessage()}");
        }
    }

    function deleteUtilisateurSanctionne($idDiscord){
        $sql = "DELETE FROM utilisateursanctionne WHERE id = :id";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la suppression d'un utilisateur sanctionné : {$ex->getMessage()}");
        }
    }
?>