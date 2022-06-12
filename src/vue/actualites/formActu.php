<?php 
    $title = $nomAction;
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
    require_once('template.php');

    if (isset($_GET['id'])) {
        $dateDerMod = new \DateTime('dateDerMod');
        $dateDerModStr = $dateDerMod->format('Y-m-d H:i:s');

        $_POST["dateDerMod"] = $dateDerModStr;
        
    } else {
        $dateCreation = new \DateTime('dateCrea');
        $dateCreationStr = $dateCreation->format('Y-m-d H:i:s');

        $dateDerMod = new \DateTime('dateDerMod');
        $dateDerModStr = $dateDerMod->format('Y-m-d H:i:s');

        $_POST["dateCreation"] = $dateCreationStr;
        $_POST["dateDerMod"] = $dateDerModStr;
    }

?>