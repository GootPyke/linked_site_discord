<?php 
    function obtenirNbPages($tableau, $nbUtilisateursParPage){
        $nbUtilisateurs = count($tableau);
        $nbPages = ceil($nbUtilisateurs / $nbUtilisateursParPage);

        if ($nbPages == 0) {
            $nbPages = 1;
        }

        return $nbPages;
    }
?>