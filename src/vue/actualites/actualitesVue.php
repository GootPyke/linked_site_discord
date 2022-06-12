<?php 
    $title = 'Actualités';
    ob_start();
?>

<div id='p-admin'>
    <h2>Panneau administratif</h2>
    <a href="">Nouvelle actualité</a>
</div>

<div id='actualites'>
<?php 
    foreach ($actualites as $actu) {
?>
    <div class="actu">
        <?php 
            
            echo'<h1>'. $actu->getTitre() .'</h1>';
            echo'<h5>Le ' . $actu->getDateCreation() . ' • Dernière modification le ' . $actu->getDateDerniereModification() . '</h5>';

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