<?php 
    $title = 'Actualités';
    ob_start();
?>

<div id='p-admin'>
    <h2>Panneau administratif</h2>
    <a href="index.php?action=formulaireActu">Nouvelle actualité</a>
</div>

<div id='actualites'>
<?php 
    foreach ($actualites as $actu) {
?>
    <div class="actu">
        <?php 
            echo'<a id="editer" href="index.php?action=formulaireActu&id='. $actu->getId().'">Éditer <img src="src/images/editer.svg" alt="Éditer"></a>';
            echo'<a id="supprimer" href="index.php?action=supprimerActu&id='. $actu->getId() .'">Supprimer <img src="src/images/supprimer.svg" alt="Supprimer"></a>';

            echo"<div class='entete'>";
                echo'<h1>'. $actu->getTitre() .'</h1>';
                echo'<h5>Le ' . $actu->getDateCreation() . ' • Dernière modification le ' . $actu->getDateDerniereModification() . '</h5>';
            echo"</div>";

            echo'<p>'. $actu->getTexte() .'</p>';
        ?>
        <div class='btn-admin'>
            <?php 
                
            ?>
        </div>
    </div>     
    
<?php 
    }
?>
        
</div>
<?php 
    $content = ob_get_clean();
    require_once 'src/vue/template.php';
?>