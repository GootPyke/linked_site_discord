<?php 
    $title = $nomAction;
    date_default_timezone_set('Europe/Paris');   
    ob_start();
?>

<form action="<?= $lienActionForm ?>" method="post">
    <h1><?= $nomAction ?></h1>

    <label for="titreActu">Titre de l'actualité</label>
    <input type="text" name="titreActu" id="titreActu" value='<?= $titre ?>'>

    <label for="texteActu">Texte de l'actualité</label>
    <input type="text" name="texteActu" id="texteActu" value='<?= $texte ?>'>

    <button type="submit">Valider <img src="src/images/valider.svg" alt="Valider"></button>
</form>

<?php 
    $content = ob_get_clean();
    require_once('src/vue/template.php');
?>