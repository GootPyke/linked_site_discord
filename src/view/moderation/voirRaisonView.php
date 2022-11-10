<?php 
    $title = 'Raison du bannissement de ' . $user->getUsername();
    ob_start();
?>

<div class="div-body-raison">
    <div>
        <a href='index.php?action=moderation&menu=utilisateursBannis' class='retour'><img src="src/images/flechegauche.svg" alt="Flèche gauche"><p>Retour</p></a>
    </div>
    <div>
        <fieldset>
            <div>
                <h1> <?= $sanction->getTypeSanction() ?> de <?= $user->getUsername() ?><p># <?= $user->getDiscriminator() ?></p> :</h1>
                
            </div>
    
            <div>
                <p>Raison : <?= $sanction->getRaison() ?></p>
            </div>

            <div>
                <h4>Sanction créée le <?= $sanction->getDateSanction() ?></h4>
            </div>

            <div>
                <a href="<?= $lienDeban ?>">Débannir</a>
            </div>
        </fieldset>
    </div>
</div>

<?php 
    $content = ob_get_clean();
    require 'src/view/template.php';
?>