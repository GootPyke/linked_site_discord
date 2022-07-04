<?php 
    $title = "Historique des sanctions";
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
 
    <div class='sanctions'>
        <?php 
        foreach ($sanctions as $sanction) {
            foreach ($utilisateursS as $utilisateurS) {
                if ($sanction->getIdDiscord() === $utilisateurS->getIdDiscord()) {
        ?>
                    <div class="sanction">
                        <div class="photoMembreSanc">
                            <?php 
                            $img = CDN_AVATAR_REFERENCE . $utilisateurS->getIdDiscord() . '/' . $utilisateurS->getAvatarHash() . '.png';
                
                            if (strpos($img, 'a_')) {
                                $img = CDN_AVATAR_REFERENCE . $utilisateurS->getIdDiscord() . '/' . $utilisateurS->getAvatarHash() . '.gif';
                            }

                            echo '<p><img src="'. $img .'" alt=""></p>';
                            ?>
                        </div>

                        <div class="pseudoSanc">
                            <?php 
                            echo '<h1>'. $utilisateurS->getUsername() .'<p>#'. $utilisateurS->getDiscriminator() .'</p></h1>';
                            ?>
                        </div>

                        <div class="typeSanction">
                            <?php 
                            echo "<p>" . $sanction->getTypeSanction() . "</p>";
                            ?>
                        </div>

                        <div class="dateSanction">
                            <?php 
                            echo "<p>Sanctionné le " . $sanction->getDateSanction() . "</p>";
                            ?>
                        </div>

                        <div class="raisonSanc">
                            <?php 
                            echo "<a href=''>Raison</a>";
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
    $modContent =ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>