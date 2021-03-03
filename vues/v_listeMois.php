<?php
/**
 * Vue Liste des mois
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Dvorah Simha Touati
 * @author    Beth Sefer
 */
?>
<h2>Mes fiches de frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un mois : </h3>
    </div>
    <div class="col-md-4">
      
        <form action="index.php?uc=etatFrais&action=selectionnerMois" /> 
              method="post" role="form">
              <div class="form-group">
                <label for="lstMois" accesskey="n">Mois : </label> 
                <select id="lstMois" name="lstMois" class="form-control"> 
                    <?php //a mettre tt ce qui est pas html , par ex parcourir tablo mois 
                
                    foreach ($lesMois as $unMois) {//on renome pr la lisibilité; foreach:boucle qui ser a parcourir un tablo ligne par ligne

                        $mois = $unMois['mois']; //declare mois et je met le tablo un mois a lindice mois                      
                        $numAnnee = $unMois['numAnnee'];
                        $numMois = $unMois['numMois'];
                        
                        if ($mois == $moisASelectionner) {//il permet de savoir quest ce que j'ai selectionner.
                            ?>
                            <option selected value="<?php echo $mois ?>"> <!--permet de nous dire si on choisi la meme chose qui etait par defaut-->
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $mois ?>"> 
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?> 
                </select>
            </div>
           <input id="ok" type="submit" value="Valider" class="btn btn-success" /> <!--submit:envoyer ; reset: reinitialiser-->
                   role="button"> <!--c pr dire le type delement-->
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" <!--afiche un element selon le role-->
                   role="button">
        </form>

    </div>
</div>