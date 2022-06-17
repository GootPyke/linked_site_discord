<?php 
    $title = 'Modération';
    ob_start();
?>

<div id='mod-body'>
    <div class="menu">
        <a href="index.php?action=moderation">Membres</a>
        <a href="">Prochainement</a>
    </div>

    <div class='membres'>
        <?php 
        foreach ($realMembers as $realMember) {
        ?>
        <div class="membre">
            <?php 
            $img = 'https://cdn.discordapp.com/avatars/' . $realMember->user->id . '/' . $realMember->user->avatar . '.png';
    
            if (strpos($img, 'a_')) {
                $img = 'https://cdn.discordapp.com/avatars/' . $realMember->user->id . '/' . $realMember->user->avatar . '.gif';
            }
            ?>
            <div class='div-img'>
            <?php 
            echo '<p><img src="'. $img .'" alt=""></p>';
            ?>
            </div>

            <div class="div-username">
            <?php 
            echo '<h1>'. $realMember->user->username.'<p>#'. $realMember->user->discriminator .'</p></h1>';
            ?>
            </div>
            <div class="div-expu">
            <?php
            echo '<a class="expulser" href="">Expulser</a>';
            ?>
            </div>
            <div class="div-ban">
            <?php 
            echo '<a class="bannir" href="">Bannir</a>';
            ?>
            </div>
        </div>
        <?php 
        }
        ?>    
    </div>
</div>

<?php 
    $content = ob_get_clean();
    require_once 'src/vue/template.php';
?>