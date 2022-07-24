<?php 
    $title = "Historique des sanctions";
    ob_start();
?>

<div id='div-generale-m'>
    <div id="tdb-hs">
        <!-- tri -->
        <div id='tdb-hs-tri'>
            <form action="index.php?action=histoSanc&tri=<?= $tri ?>&page=1&filtre=<?= $filtre ?>" method="post">
                <p>Tri :</p>

                <select name="tri-sel" id="tri-sel" onChange="this.form.submit()">
                    <option value="<?= $tri ?>" style="display:none"><?= $nomCurrentTri ?></option>

                    <option value="lastDESC"
                    <?php 
                        if ($tri === "lastDESC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >Antéchronologique</option>

                    <option value="lastASC"
                    <?php 
                        if ($tri === "lastASC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >Chronologique</option>

                    <option value="pseudoASC"
                    <?php 
                        if ($tri === "pseudoASC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >De A à Z</option>

                    <option value="pseudoDESC"
                    <?php 
                        if ($tri === "pseudoDESC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >De Z à A</option>

                    <option value="discrimASC"
                    <?php 
                        if ($tri === "discrimASC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >Discriminateur croissant</option>

                    <option value="discrimDESC"
                    <?php 
                        if ($tri === "discrimDESC") {
                            echo 'style="display:none"';
                        }
                    ?>
                    >Discriminateur décroissant</option>


                </select>
            </form>
        </div>

        <!-- filtres -->
        <div id='tdb-hs-filtre'>
            <form action="index.php?action=histoSanc&tri=<?= $tri ?>&page=1" method="post">
                <p>Filtrage par type de sanction :</p>

                <div id='filtre-div-checks'>
                    <div class="toggle-pill-color">
                        <input type="checkbox" name="ban-check" id="ban-check" value="<?= $valBan ?>" onChange="this.form.submit()" 
                        <?php 
                            if ($valBan === 1) {
                                echo "checked";
                            }
                        ?>
                        >
                        <label for="ban-check"></label>
                        <label for="ban-check" class="nomSancFiltre">Bannissements</label>
                    </div>
    
                    <div class="toggle-pill-color">
                        <input type="checkbox" name="expu-check" id="expu-check" value="<?= $valExpu ?>" onChange="this.form.submit()" 
                        <?php 
                            if ($valExpu === 1) {
                                echo "checked";
                            }
                        ?>
                        >
                        <label for="expu-check"></label>
                        <label for="expu-check" class="nomSancFiltre">Expulsions</label>
                    </div>
    
                    <div class="toggle-pill-color">
                        <input type="checkbox" name="avert-check" id="avert-check" value="<?= $valAvert ?>" onChange="this.form.submit()" 
                        <?php 
                            if ($valAvert === 1) {
                                echo "checked";
                            }
                        ?>
                        >
                        <label for="avert-check"></label>
                        <label for="avert-check" class="nomSancFiltre">
                            Avertissements
                        </label>
                    </div>
                </div>
            </form>
        </div>  

        <!-- pagination -->
        <div id='tdb-hs-page'>
            <?php 
            echo "<a href='index.php?action=histoSanc&tri=" . $tri ."&filtre=". $filtre . "&page=". $page-1 . "'><img src='src/images/fle_gauche.svg' alt='flèche gauche'></a>";
            
            echo"<p id='infoPage'>Page " . $page . " sur " . $nbPages ."</p>";

            echo "<a href='index.php?action=histoSanc&tri=" . $tri ."&filtre=". $filtre . "&page=". $page+1 . "'><img src='src/images/fle_droite.svg' alt='flèche droite'></a>";
            ?>
        </div>

        <!-- recherche -->
        <div id='tdb-hs-rech'>
            <form action="<?= $lienActionFrom ?>" method="post">
                <label for="rech">Recherche</label>
                <input type="text" name="rech" id="rech">
            </form>
        </div>
    </div>
 
    <div class='sanctions'>
        <?php 
        for ($i = 0; $i < $nbSanctionsParPage; $i++) { 
            if (isset($sanctions[$i]) === false) {
                continue;
            }
            foreach ($utilisateursS as $utilisateurS) {
                if ($sanctions[$i]->getIdDiscord() === $utilisateurS->getIdDiscord()) {
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
                            echo "<p>" . $sanctions[$i]->getTypeSanction() . "</p>";
                            ?>
                        </div>

                        <div class="dateSanction">
                            <?php 
                            echo "<p>Sanctionné le " . $sanctions[$i]->getDateSanction() . "</p>";
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

<!-- <script src="src/scripts/filtres.js"></script> -->

<?php 
    $modContent = ob_get_clean();
    require_once 'src/view/modTemplate.php';
?>