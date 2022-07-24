<?php 
    $title = 'Liste des membres';
    ob_start();
?>

<div id='div-generale-m'>
    <div id='gestion'>
        <div id="tri">
            <form action="index.php?action=moderation&tri=<?= $tri ?>&page=1" method="post">
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
            echo "<a href='index.php?action=moderation&tri=" . $tri ."&page=". $page-1 . "'><img src='src/images/fle_gauche.svg' alt='flèche gauche'></a>";
            
            echo"<p id='infoPage'>Page " . $page . " sur " . $nbPages ."</p>";
    
            echo "<a href='index.php?action=moderation&tri=" . $tri . "&page=" . $page+1 . "'><img src='src/images/fle_droite.svg' alt='flèche droite'></a>";
            ?>
        </div>
    </div>

    <div class='membres'>
        <?php 
            foreach ($realMembers as $realMember) {
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
        ?>  
    </div>
</div>

<?php 
    $modContent = ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>