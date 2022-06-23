<?php 
require_once "src/model/connexionBDDModel.php";
    
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
                $dateSanction = $donnee->getDateSanction();
                
                $dateSanctionNew = strftime("%d %B %G à %Hh%M", strtotime($dateSanction));
                
                $donnee->setDateSanction($dateSanctionNew);
            }
        } catch (PDOException $ex) {
            var_dump("Erreur lors de l'obtention des sanctions : {$ex->getMessage()}");
        } finally {
            return $data;
        }
    }
    
    function getSanctionById($id){
        $sql = "SELECT * FROM sanction WHERE id= :id";
        $data = "";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->setFetchMode(PDO::FETCH_CLASS, 'Sanction');

            $req->bindValue(':id', $id, PDO::PARAM_INT);

            $req->execute();

            $data = $req->fetch();
            
        } catch (PDOException $ex) {
            var_dump("Erreur pour l'obtention d'une sanction par son identifiant: {$ex->getMessage()}");
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

    function editSanction($id, $idDiscord, $raison){
        $sql = "UPDATE actualite SET titre= :titre, texte= :texte, dateDerniereModification= :dateDerniereModification WHERE id= :id";

        try {
            $bdd=connexionBDD();

            $req=$bdd->prepare($sql);

            $req->bindValue('id', $id, PDO::PARAM_INT);
            $req->bindValue('titre', $titre, PDO::PARAM_STR);
            $req->bindValue('texte', $texte, PDO::PARAM_STR);
            $req->bindValue('dateDerniereModification', $dateDerniereModification, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la modification d'une actualité : {$ex->getMessage()}");
        }
    }

    function deleteActualite($id){
        $sql = "DELETE FROM actualite WHERE id = :id";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':id', $id, PDO::PARAM_INT);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de la suppression de l'actualité : {$ex->getMessage()}");
        }
    }
?>