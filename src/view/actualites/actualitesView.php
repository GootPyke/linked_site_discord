<?php 
    $title = 'Actualités';
    ob_start();
?>
    <h1 id='titrePage'>Actualités</h1>
<?php 
    if ($estAdmin === true) {
?>
        <div id='p-admin'>
            <a href="index.php?action=formulaireActu"><img src="src/images/ajouter.svg" alt="Ajouter"><p>Nouvelle actualité</p></a>
        </div>
<?php 
    }
?>

<div id='actualites'>
<?php 
    foreach ($actualites as $actu) {
?>
    <div class="actu">
        <?php 
        if ($estAdmin === true) {
            echo'<a id="editer" href="index.php?action=formulaireActu&id='. $actu->getId().'">Éditer <img src="src/images/editer.svg" alt="Éditer"></a>';
            echo'<a id="supprimer" href="index.php?action=supprimerActu&id='. $actu->getId() .'">Supprimer <img src="src/images/supprimer.svg" alt="Supprimer"></a>';
        }

            echo"<div class='entete'>";
                echo'<h1>'. $actu->getTitre() .'</h1>';
                echo'<h5>Le ' . $actu->getDateCreation() . ' • Dernière modification le ' . $actu->getDateDerniereModification() . '</h5>';
            echo"</div>";

            echo'<p>'. $actu->getTexte() .'</p>';
        ?>
    </div>     
    
<?php 
    }
?>
        
</div>
<?php 
    $content = ob_get_clean();
    require_once 'src/view/template.php';
?>