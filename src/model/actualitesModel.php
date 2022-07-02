<?php 
    require_once "src/model/connexionBDDModel.php";
    
    class Actualite{
        private $id;
        private $titre;
        private $texte;
        private $dateCreation;
        private $dateDerniereModification;
        
        public function getId() {return $this->id;}
        
        public function getTitre() {return $this->titre;}
        public function setTitre($titre) {$this->titre = $titre;}
        
        public function getTexte() {return $this->texte;}
        public function setTexte($texte) {$this->texte = $texte;}
        
        public function getDateCreation() {return $this->dateCreation;}
        public function setDateCreation($dateCreation) {$this->dateCreation = $dateCreation;}
        
        public function getDateDerniereModification() {return $this->dateDerniereModification;}
        public function setDateDerniereModification($dateDerniereModification) {$this->dateDerniereModification = $dateDerniereModification;}
    }
    
    //Obtenir toutes les actualités en gros quoi
    function getAllActualites(){
        $sql = "SELECT * FROM actualite ORDER BY id DESC";
        $data = [];

        try {
            $bdd = connexionBDD();
            
            $req = $bdd->prepare($sql);
            
            $req->setFetchMode(PDO::FETCH_CLASS, 'Actualite');
            
            $req->execute();
            
            $data = $req->fetchAll();
            
            foreach ($data as $donnee) {
                $dateCrea = $donnee->getDateCreation();
                $dateDerMod = $donnee->getDateDerniereModification();
                
                $dateCreaNew = strftime("%d %B %G à %Hh%M", strtotime($dateCrea));
                $dateDerModNew = strftime("%d %B %G à %Hh%M", strtotime($dateDerMod));
                
                $donnee->setDateCreation($dateCreaNew);
                $donnee->setDateDerniereModification($dateDerModNew);
            }
        } catch (PDOException $ex) {
            var_dump("Erreur lors de l'obtention des actualités : {$ex->getMessage()}");
        } finally {
            return $data;
        }
    }
    
    //Obtenir une actualité par son identifiant
    function getActualiteById($id){
        $sql = "SELECT * FROM actualite WHERE id= :id";
        $data = "";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->setFetchMode(PDO::FETCH_CLASS, 'Actualite');

            $req->bindValue(':id', $id, PDO::PARAM_INT);

            $req->execute();

            $data = $req->fetch();
            
        } catch (PDOException $ex) {
            var_dump("Erreur pour l'obtention d'une actualité par son identifiant: {$ex->getMessage()}");
        } finally {
            return $data;
        }
    }

    //Ajouter une actualité
    function addActualite($titre, $texte, $dateCreation, $dateDerniereModification){
        $sql = "INSERT INTO actualite(titre, texte, dateCreation, dateDerniereModification) VALUES (:titre, :texte, :dateCreation, :dateDerniereModification)";

        try {
            $bdd = connexionBDD();

            $req = $bdd->prepare($sql);

            $req->bindValue(':titre', $titre, PDO::PARAM_STR);
            $req->bindValue(':texte', $texte, PDO::PARAM_STR);
            $req->bindValue(':dateCreation', $dateCreation, PDO::PARAM_STR);
            $req->bindValue(':dateDerniereModification', $dateDerniereModification, PDO::PARAM_STR);

            $req->execute();
        } catch (PDOException $ex) {
            var_dump("Erreur lors de l'ajout d'une actualité : {$ex->getMessage()}");
        }
    }

    //Éditer une actualité, sans modification de la date de création
    function editActualite($id, $titre, $texte, $dateDerniereModification){
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

    // Supprimer une actualité
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