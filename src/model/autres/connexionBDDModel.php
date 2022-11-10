<?php 
    function connexionBDD(){
        $chaineDeConnexion ="mysql:host=localhost;dbname=sitediscord;charset=utf8mb4";

        try {
            $bdd = new PDO($chaineDeConnexion, 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $ex) {
            die(print_r($bdd->errorInfo()));
        } finally {
            return $bdd;
        }
    }
?>