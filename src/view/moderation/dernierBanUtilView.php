<?php 
    $title = 'Raison du bannissement de ' . $user->user->username;
    ob_start();
?>

<div class="div-body-raison">
    <a href='index.php?action=membresBannis' class='retour'><img src="src/images/flechegauche.svg" alt="Flèche gauche"><p>Retour</p></a>
    <div>
        <fieldset>
            <?php 
            echo "<div>";
            echo "<h1>Raison du bannissement de " . $user->user->username . " :</h1>";
            echo "</div>";
    
            echo "<div>";
            echo "<p>" . $sanction->getRaison() . "</p>";
            echo "</div>";
    
            echo "<div>";
            echo "<h4>Sanction créée le " . $sanction->getDateSanction() . "</h4>";
            echo "</div>";

            echo "<div>";
            echo '<a href="index.php?action=sanction&sanction=deban&id='. $user->user->id .'">Débannir</a>';
            echo "</div>";
            ?>
        </fieldset>
    </div>
</div>

<?php 
    $content = ob_get_clean();
    require 'src/view/template.php'
?>