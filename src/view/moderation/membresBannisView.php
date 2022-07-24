<?php 
    $title = 'Liste des utilisateurs bannis';
    ob_start();
?>

<div id='div-generale-m'>
    <div id='gestion'>
        <div id="tri">
            <form action="index.php?action=membresBannis&tri=<?= $tri ?>&page=1" method="post">
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
        </div>
    
        <!-- pagination -->
        <div id='pages'>
            <?php 
            echo "<a href='index.php?action=membresBannis&tri=" . $tri ."&page=". $page-1 . "'><img src='src/images/fle_gauche.svg' alt='flèche gauche'></a>";
            
            echo"<p id='infoPage'>Page " . $page . " sur " . $nbPages ."</p>";
    
            echo "<a href='index.php?action=membresBannis&tri=" . $tri . "&page=" . $page+1 . "'><img src='src/images/fle_droite.svg' alt='flèche droite'></a>";
            ?>
        </div>
    </div>

    <div class='membres'>
        <?php 
            foreach ($membresBannis as $membreBanni) {
        ?>
                <div class="membre">
                    <?php 
                    $img = CDN_AVATAR_REFERENCE . $membreBanni->user->id . '/' . $membreBanni->user->avatar . '.png';
            
                    if (strpos($img, 'a_')) {
                        $img = CDN_AVATAR_REFERENCE . $membreBanni->user->id . '/' . $membreBanni->user->avatar . '.gif';
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
        ?>  
    </div>
</div>


<?php 
    $modContent = ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>