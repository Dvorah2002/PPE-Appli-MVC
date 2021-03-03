<?php

/**
 * Vues Valider Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Dvorah Simha Touati
 * @author    Beth Sefer
 */
?>

<form action="index.php?uc=validerFrais&action=corrigerFrais" role="form" method="post">
   <div class="col-md-4">
        <?php//Liste déroulante des visiteurs?>
        <div class="form-group" style="display: inline-block">
            <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
            <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                <?php
                foreach ($lesVisiteurs as $unVisiteur) {
                    $id = $unVisiteur['id'];
                    $nom = $unVisiteur['nom'];
                    $prenom = $unVisiteur['prenom'];
                    if ($id == $levisiteurASelectionner) {
                        ?>
                        <option selected value="<?php echo $id ?>">
                            <?php echo $nom . ' ' . $prenom ?> </option>
                        <?php
                    } else {
                        ?>
                        <option value="<?php echo $id ?>">
                            <?php echo $nom . ' ' . $prenom ?> </option>
                        <?php
                    }
                }
                ?>    

            </select>
        </div>
           
        <?php//liste déroulante des mois?>          
        &nbsp;<div class="form-group" style="display: inline-block">
            <label for="lstMois" accesskey="n">Mois : </label>
            <select id="lstMois" name="lstMois" class="form-control">
                <?php
                foreach ($lesMois as $unMois) {
                    $mois = $unMois['mois'];
                    $numAnnee = $unMois['numAnnee'];
                    $numMois = $unMois['numMois'];
                    if ($mois == $moisASelectionner) {
                        ?>
                        <option selected value="<?php echo $mois ?>">
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
    </div> <br><br><br><br>
    
    
<?php//elements forfaitisés?>
   
 <div class="valide">
   <div class="row"> 
     <h2 style="color:orange">&nbsp;Valider la fiche de frais</h2>
     <h3>&nbsp;&nbsp;Eléments forfaitisés</h3>
     <div class="col-md-4">   
               <?php
               foreach ($lesFraisForfait as $unFrais) {
                   $idFrais = $unFrais['idfrais'];
                   $libelle = htmlspecialchars($unFrais['libelle']);
                   $quantite = $unFrais['quantite']; ?>
                   <div class="form-group">
                       <label for="idFrais"><?php echo $libelle ?></label>
                       <input type="text" id="idFrais"
                               name="lesFrais[<?php echo $idFrais ?>]"
                              size="10" maxlength="5"
                              value="<?php echo $quantite ?>"
                              class="form-control">
                   </div>
                   <?php
               }
               ?>
               <button class="btn btn-success" type="edit" >Corriger</button>
               <button class="btn btn-danger" type="reset">Reinitialiser</button>
     </div>
     </div>
  </div>
</form>

<?php//elements non forfaitises?>
<form  role="form" method="post">
    <input type="hidden" id="lstVisiteurs" name="lstVisiteurs" class="form-control" value= "<?php echo $levisiteurASelectionner ?>">
    <input type="hidden" id="lstMois" name="lstMois" class="form-control" value= "<?php echo $moisASelectionner ?>">
     <h3>Eléments non forfaitisés</h3>
    <div class="panel panel-info1" >
        <div class="panel-heading" style="color:white">Descriptif des éléments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date" style="border-bottom: 1px solid #ff6f02; border-right:1px solid #ff6f02; " >Date</th>
                    <th class="libelle" style="border-bottom: 1px solid #ff6f02;border-right:1px solid #ff6f02;">Libellé</th>  
                    <th class="montant" style="border-bottom: 1px solid #ff6f02; border-right:1px solid #ff6f02;">Montant</th>  
                    <th class="action" style="border-bottom: 1px solid #ff6f02; ">&nbsp;</th>
                </tr>
            </thead> 
            <tbody >
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $idFHF = $unFraisHorsForfait['id']; ?>    
            <input type="hidden" name="idFHF" id="frais" size="10" value="<?php echo $idFHF ?>" class="form-control"/>
                <tr > 
                    <td style="border-right:1px solid #ff6f02;"><input type="text" name="date" id="date" size="10" value="<?php echo $date ?>" class="form-control"/></td>
                    <td style="border-right:1px solid #ff6f02;"><input type="text" name="libelle" id="libelle" size="10" value="<?php echo $libelle ?>" class="form-control"/></td>
                    <td style="border-right:1px solid #ff6f02;"><input type="text" name="montant" id="montant" size="10" value="<?php echo $montant ?>" class="form-control"/></td>
                    
                    <td><button class="btn btn-success" type="edit"action="index.php?uc=validerFrais&action=corrigerFraisHF">Corriger</button>
                        <button class="btn btn-danger" type="reset">Reinitialiser</button>
                        <button class="btn btn-danger" type="reset"action="index.php?uc=validerFrais&action=reporter"onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">Reporter</button>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</form>
    
                    
<form method="post" 
              action="index.php?uc=validerFrais&action=validerFrais" 
              role="form">
    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $levisiteurASelectionner ?>">
    <input id="ok" type="submit" value="Valider" class="btn btn-success" 
            role="button">
    <button class="btn btn-danger" type="reset">Reinitialiser</button>
</form>
<form method="post" 
              action="index.php?uc=validFrais&action=validerFiche" 
              role="form">
    <input name="lstMois" type="hidden" id="lstMois" class="form-control" value="<?php echo $moisASelectionner ?>">
    <input name="lstVisiteurs" type="hidden" id="lstVisiteurs" class="form-control" value="<?php echo $visiteurASelectionner ?>">
      Nombre de justificatifs: <input type="text" id="nbJust" name="nbJust" class="form-control-me" value="<?php echo $nbJustificatifs ?>"><br><br> 
    <input id="ok" type="submit" value="Valider" class="btn btn-success" 
            role="button">
    <button class="btn btn-danger" type="reset">Reinitialiser</button>
</form>
    