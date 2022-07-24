<?php 

    // Cette fonction sert à transformer une date en français.
    // Je trouve que la documentation est incompréhensible et pénible à comprendre, c'était mieux avant.
    function transformerDateEnFrancais($date){
        $moisAnglais = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $moisFrancais = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

        return str_replace($moisAnglais, $moisFrancais, $date);
    }
?>