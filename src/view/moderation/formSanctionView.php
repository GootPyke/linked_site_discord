<?php 
    $title = $nomAction . $pseudo . $discriminateur;
    ob_start();
?>

<div id='form-sanction'>
    <a href='index.php?action=moderation' class='retour'><img src="src/images/flechegauche.svg" alt="Flèche gauche"><p>Retour</p></a>

    <form action="<?= $lienActionForm ?>" method="post">
        <h1><?= $nomAction . "<h2>" . $pseudo . "</h2>" . "<h3>" . $discriminateur . "</h3>" ?></h1>

        <div>
            <!-- <div>

            </div> -->
            <div id='div-raison'>
                <label for="raison">RAISON</label>
                <textarea name="raison" id="raison" cols="30" rows="5" required></textarea>
            </div>
        </div>

        <div id='btn-form'>
            <button type="submit"><p>Valider</p><img src="src/images/valider.svg" alt="Valider"></button>
        </div>
    </form>
</div>

<?php 
    $content = ob_get_clean();
    require_once 'src/view/template.php';
?>