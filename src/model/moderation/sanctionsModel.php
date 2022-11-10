<?php 
require_once SITE_ROOT . "/src/model/autres/connexionBDDModel.php";
require_once SITE_ROOT . "/src/helper/dateHelper.php";
    
    class Sanction{
        private $id;
        private $idDiscord;
        private $typeSanction;
        private $raison;
        private $dateSanction;
        
        public function getId() {return $this->id;}
        
        public function getIdDiscord() {return $this->idDiscord;}
        public function setIdDiscord($idDiscord) {$this->idDiscord = $idDiscord;}
        
        public function getTypeSanction() {return $this->typeSanction;}
        public function setTypeSanction($typeSanction) {$this->typeSanction = $typeSanction;}
        
        public function getRaison() {return $this->raison;}
        public function setRaison($raison) {$this->raison = $raison;}
        
        public function getDateSanction() {return $this->dateSanction;}
        public function setDateSanction($dateSanction) {$this->dateSanction = $dateSanction;}
    }
    
    //Obtenir toutes les sanctions pour les afficher
    function getAllSanctions(){
        $sql = "SELECT * FROM sanction ORDER BY id DESC";
        $data = [];
        
        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);
            
            $req->setFetchMode(PDO::FETCH_CLASS, 'Sanction');
            
            $req->execute();
            
            $data = $req->fetchAll();
            
            foreach ($data as $donnee) {
                $dateSanction = date_create_from_format('Y-m-d H:i:s', $donnee->getDateSanction());
                $dateFormatee = $dateSanction->format('d F Y \à H\hi');
                $dateFormateeFinale = transformerDateEnFrancais($dateFormatee);
                $donnee->setDateSanction($dateFormateeFinale);
            }
        } catch (PDOException $ex) {
            return false;
        } finally {
            return $data;
        }
    }

    function getSanctionById($id){
        $sql = "SELECT * FROM sanction WHERE id = :id";
        $data = "";
        
        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);
            
            $req->setFetchMode(PDO::FETCH_CLASS, 'Sanction');

            $req->bindValue(':id', $id, PDO::PARAM_INT);
            
            $req->execute();
            
            $data = $req->fetch();
            
            $dateSanction = date_create_from_format('Y-m-d H:i:s', $data->getDateSanction());
            $dateFormatee = $dateSanction->format('d F Y \à H\hi');
            $dateFormateeFinale = transformerDateEnFrancais($dateFormatee);
            $data->setDateSanction($dateFormateeFinale);
        } catch (PDOException $ex) {
            return false;
        } finally {
            return $data;
        }
    }

    function requeteInfoSanctionSQL($sql){
        $data = "";
        
        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);
            
            $req->execute();
            
            $data = $req->fetch();
            
        } catch (PDOException $ex) {
            return false;
        } finally {
            return $data[0];
        }
    }
    
    //Obtenir toutes les sanctions de l'utilisateur
    function getUserSanctionsByIdDiscord($tabIdDiscord){
        $questionmarks = str_repeat("?,", count($tabIdDiscord)-1) . "?";
        $sql = "SELECT * FROM sanction WHERE idDiscord IN ($questionmarks)";
        $data = array();

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->setFetchMode(PDO::FETCH_CLASS, 'Sanction');

            $req->execute($tabIdDiscord);

            $data = $req->fetchAll();

            foreach ($data as $donnee) {
                $dateSanction = date_create_from_format('Y-m-d H:i:s', $donnee->getDateSanction());
                $dateFormatee = $dateSanction->format('d F Y \à H\hi');
                $dateFormateeFinale = transformerDateEnFrancais($dateFormatee);
                $donnee->setDateSanction($dateFormateeFinale);
            }

        } catch (PDOException $ex) {
            var_dump("Erreur pour l'obtention des sanctions de l'utilisateur par son identifiant Discord : {$ex->getMessage()}");
        } finally {
            return $data;
        }
    }

    //Obtenir le dernier bannissement s'il y en a un d'un utilisateur
    function getUserLastBan($idDiscord){
        $sql = "SELECT * FROM sanction WHERE typeSanction='Bannissement' AND idDiscord=:idDiscord LIMIT 1";
        $data = "";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->setFetchMode(PDO::FETCH_CLASS, 'Sanction');

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);

            $req->execute();

            $data = $req->fetch();

            $dateSanction = date_create_from_format('Y-m-d H:i:s', $data->getDateSanction());
            $dateFormatee = $dateSanction->format('d F Y \à H\hi');
            $dateFormateeFinale = transformerDateEnFrancais($dateFormatee);
            $data->setDateSanction($dateFormateeFinale);
            
        } catch (PDOException $ex) {
            var_dump("Erreur pour l'obtention du dernier bannissement de l'utilisateur par son identifiant Discord : {$ex->getMessage()}");
        } finally {
            return $data;
        }
    }

    function addSanction($idDiscord, $typeSanction, $raison, $dateSanction){
        $sql = "INSERT INTO sanction(idDiscord, typeSanction, raison, dateSanction) VALUES (:idDiscord, :typeSanction, :raison, :dateSanction)";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':idDiscord', $idDiscord, PDO::PARAM_STR);
            $req->bindValue(':typeSanction', $typeSanction, PDO::PARAM_STR);
            $req->bindValue(':raison', $raison, PDO::PARAM_STR);
            $req->bindValue(':dateSanction', $dateSanction, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de l'ajout d'une sanction : {$ex->getMessage()}");
        }
    }

    function deleteSanction($idDiscord){
        $sql = "DELETE FROM sanction WHERE idDiscord = :idDiscord";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':id', $idDiscord, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la suppression d'une sanction : {$ex->getMessage()}");
        }
    }
?>