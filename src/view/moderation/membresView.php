<?php 
    $title = 'Liste des membres';
    ob_start();
?>

<div id='div-generale-m'>
    <div id="tri">
        <p>Tri :</p>
        <div>
            <a href="index.php?action=moderation&tri=nickname">Par pseudonyme</a>
            <p>-</p>
            <a href="index.php?action=moderation&tri=discrim">Par discriminateur</a>
        </div>
    </div>

    <div class='membres'>
        <?php 
        foreach ($tabTri as $value) {
            foreach ($realMembers as $realMember) {
                if ($tri === 'nickname') {
                    $tabKey = $realMember->user->username;
                } else {
                    $tabKey = $realMember->user->discriminator;
                }

                if ($value === $tabKey) {
        ?>
                    <div class="membre">
                        <?php 
                        $img = CDN_AVATAR_REFERENCE . $realMember->user->id . '/' . $realMember->user->avatar . '.png';
                
                        if (strpos($img, 'a_')) {
                            $img = CDN_AVATAR_REFERENCE . $realMember->user->id . '/' . $realMember->user->avatar . '.gif';
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
                        echo '<a class="expulser" href="index.php?action=sanction&sanction=expulser&id='. $realMember->user->id .'">Expulser</a>';
                        ?>
                        </div>

                        <div class="div-ban">
                        <?php 
                        echo '<a class="bannir" href="index.php?action=sanction&sanction=bannir&id='. $realMember->user->id .'">Bannir</a>';
                        ?>
                        </div>
                    </div>
        <?php
                }
            }
        } 
        ?>  
    </div>
</div>

<?php 
    $modContent = ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>