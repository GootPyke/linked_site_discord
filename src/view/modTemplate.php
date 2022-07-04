<?php 
    ob_start();
?>

<div id='mod-body'>
    <div class="menu">
        <a href="index.php?action=moderation">Membres</a>
        <a href="index.php?action=membresBannis">Utilisateurs bannis</a>
        <a href="index.php?action=histoSanc">Historique des sanctions</a>
    </div>

    <?= $modContent ?>
</div>

<?php 
    $content = ob_get_clean();
    require_once "src/view/template.php";
?>