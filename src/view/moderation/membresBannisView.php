<?php 
    $title = 'Liste des utilisateurs bannis';
    ob_start();
?>

<div id='div-generale-m'>
    <div id="tri">
        <p>Tri :</p>
        <div>
            <a href="index.php?action=membresBannis&tri=nickname">Par pseudonyme</a>
            <p>-</p>
            <a href="index.php?action=membresBannis&tri=discrim">Par discriminateur</a>
        </div>
    </div>

    <div class='membres'>
        <?php 
        foreach ($tabTri as $value) {
            foreach ($membresBannis as $membreBanni) {
                if ($tri === 'nickname') {
                    $tabKey = $membreBanni->user->username;
                } else {
                    $tabKey = $membreBanni->user->discriminator;
                }

                if ($value === $tabKey) {
        ?>
                    <div class="membre">
                        <?php 
                        $img = 'https://cdn.discordapp.com/avatars/' . $membreBanni->user->id . '/' . $membreBanni->user->avatar . '.png';
                
                        if (strpos($img, 'a_')) {
                            $img = 'https://cdn.discordapp.com/avatars/' . $membreBanni->user->id . '/' . $membreBanni->user->avatar . '.gif';
                        }
                        ?>
                        <div class='div-img'>
                        <?php 
                        echo '<p><img src="'. $img .'" alt=""></p>';
                        ?>
                        </div>

                        <div class="div-username">
                        <?php 
                        echo '<h1>'. $membreBanni->user->username.'<p>#'. $membreBanni->user->discriminator .'</p></h1>';
                        ?>
                        </div>

                        <div class="div-deban">
                        <?php
                        echo '<a href="index.php?action=sanction&sanction=deban&id='. $membreBanni->user->id .'">Débannir</a>';
                        ?>
                        </div>

                        <div class="div-raison">
                        <?php 
                        echo '<a href="index.php?action=viewRaison&id='. $membreBanni->user->id .'">Raison du bannissement</a>';
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