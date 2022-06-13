<?php 
    $title = $nomAction;
    date_default_timezone_set('Europe/Paris');   
    ob_start();
?>

<div class='body-formActu'>
    <fieldset id='formActuField'>
        <form action="<?= $lienActionForm ?>" method="post">
            <h1><?= $nomAction ?></h1>
        
            <div>
                <div id='d1'>
                    <label for="titreActu">Titre de l'actualité</label>
                    <textarea name="titreActu" id="titreActu" cols="30" rows="5" maxlenght="255"><?= $titre ?></textarea>
                </div>
            
                <div id='d2'>
                    <label for="texteActu">Texte de l'actualité</label>
                    <textarea name="texteActu" id="texteActu" cols="30" rows="10"><?= $texte ?></textarea>
                </div>
            </div>
        
            <div id='btn-form'>
                <button type="submit"><p>Valider</p><img src="src/images/valider.svg" alt="Valider"></button>
            </div>
        </form>
    </fieldset>
</div>

<?php 
    $content = ob_get_clean();
    require_once('src/vue/template.php');
?>