<?php 
    $title = 'Liste des utilisateurs bannis';
    ob_start();
?>

<div id='div-generale-m'>
    <div id='gestion'>
        <div id="tri">
            <?php 
            if ($utilisateursFinaux !== false) {
            ?>
                <form action="index.php?action=moderation&menu=utilisateursBannis&recherche=<?= $termeDeRecherche ?>&page=1" method="post">
                    <p>Tri :</p>
    
                    <select name="tri-sel" id="tri-sel" onChange="this.form.submit()">
                        <option value="<?= $tri ?>" style="display:none"><?= $nomCurrentTri ?></option>
    
                        <option value="pseudoAZ"
                        <?php 
                            if ($tri === "pseudoAZ") {
                                echo 'style="display:none"';
                            }
                        ?>
                        >De A à Z</option>
    
                        <option value="pseudoZA"
                        <?php 
                            if ($tri === "pseudoZA") {
                                echo 'style="display:none"';
                            }
                        ?>
                        >De Z à A</option>
    
                        <option value="discrimCroi"
                        <?php 
                            if ($tri === "discrimCroi") {
                                echo 'style="display:none"';
                            }
                        ?>
                        >Discriminateur croissant</option>
    
                        <option value="discrimDecroi"
                        <?php 
                            if ($tri === "discrimDecroi") {
                                echo 'style="display:none"';
                            }
                        ?>
                        >Discriminateur décroissant</option>
                    </select>
                </form>
        <?php 
            }
        ?>
        </div>
    
        <!-- pagination -->
        <div id='pages'>
            <?php 
            if (($utilisateursFinaux !== false) && ($page != 1)) {
                echo "<a href='index.php?action=moderation&menu=utilisateursBannis&tri=" . $tri ."&recherche=" . $termeDeRecherche . "&page=". $page-1 . "'><img src='src/images/fle_gauche.svg' alt='flèche gauche'></a>";
            }
            
            echo"<p id='infoPage'>Page " . $page . " sur " . $nbPages ."</p>";
    
            if (($utilisateursFinaux !== false) && ($page != $nbPages)) {
                echo "<a href='index.php?action=moderation&menu=utilisateursBannis&tri=" . $tri . "&recherche=" . $termeDeRecherche . "&page=" . $page+1 . "'><img src='src/images/fle_droite.svg' alt='flèche droite'></a>";
            }
            ?>
        </div>

        <div id='recherche'>
            <form action="index.php?action=moderation&menu=utilisateursBannis&tri=<?= $tri ?>&page=1" method='post'>
                <label for="recherche">Recherche :</label>
                <div id='hs-rech'>
                    <div id='hs-r-1'>
                        <a href='index.php?action=moderation&menu=utilisateursBannis'><img src="src/images/croix.svg" alt="effacer"></a>
                    </div>
                    <input type="text" name="recherche" id="recherche" placeholder="<?= $termeDeRecherche ?>">
                    <div id='hs-r-2'>
                        <button type="submit"><img src="src/images/recherche_loupe.svg" alt="loupe"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class='membres'>
        <?php 
            if (is_array($utilisateursAAfficher) === true) {
                foreach ($utilisateursAAfficher as $utilisateurAAfficher) {
        ?>
                    <div class="membre">
                        <?php 
                        $img = CDN_AVATAR_REFERENCE . $utilisateurAAfficher->user->id . '/' . $utilisateurAAfficher->user->avatar . '.png';
                
                        if (strpos($img, 'a_')) {
                            $img = CDN_AVATAR_REFERENCE . $utilisateurAAfficher->user->id . '/' . $utilisateurAAfficher->user->avatar . '.gif';
                        }
                        ?>
                        <div class='div-img'>
                        <?php 
                        echo '<p><img src="'. $img .'" alt=""></p>';
                        ?>
                        </div>

                        <div class="div-username">
                        <?php 
                        echo '<h1>'. $utilisateurAAfficher->user->username.'<p>#'. $utilisateurAAfficher->user->discriminator .'</p></h1>';
                        ?>
                        </div>

                        <div id="act-sanc">
                            <div class="div-deban">
                            <?php
                            echo '<a href="index.php?action=moderation&menu=sanction&sanction=deban&id='. $utilisateurAAfficher->user->id .'">Révoquer</a>';
                            ?>
                            </div>
    
                            <div class="div-raison">
                                <a href="index.php?action=moderation&menu=voirRaison&idSanction=<?= $utilisateurAAfficher->sanctions['bannissement'][0]->getId() ?>">Raison</a>
                            </div>
                        </div>
                    </div>
        <?php
                }
            } else {
                echo "<p id='noResult'>" . $utilisateursAAfficher . "</p>";
            }
        ?>  
    </div>
</div>


<?php 
    $modContent = ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>