<?php 
    ob_start();
?>

<div id='mod-body'>
    <div class="menu">
        <a href="index.php?action=moderation&menu=membres" <?php if($menuMod === 'mm') {echo 'id="a-selected"';}?>>Membres</a>
        <a href="index.php?action=moderation&menu=utilisateursBannis" <?php if($menuMod === 'mb') {echo 'id="a-selected"';}?>>Utilisateurs bannis</a>
        <a href="index.php?action=moderation&menu=historiqueSanctions" <?php if($menuMod === 'hs') {echo 'id="a-selected"';}?>>Historique des sanctions</a>
    </div>

    <?= $modContent ?>
</div>

<?php 
    $content = ob_get_clean();
    require_once "src/view/template.php";
?>