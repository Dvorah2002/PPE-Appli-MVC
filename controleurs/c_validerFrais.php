<?php

/**
 * Controleur Valider Frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Dvorah Simha Touati
 * @author    Beth Sefer
 */

$mois = getMois(date('d/m/Y'));
$moisPrecedent= getmoisPrecedent($mois);

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
case 'selectionnerVetM':
    $lesVisiteurs= $pdo->getLesVisiteurs();//parametre vide prq ? parce que quand je les creer je lui est pas mis de parametre 
    $lesCles1=array_keys($lesVisiteurs);//c'est une variable qui prend la valeur d'un tablo les visiteurs;on le declare en tablo pr metre par la suite un curseur 
    $levisiteurASelectionner= $lesCles1[0];//[0] g le premier visiteur du tablo ; selon la bdd; cette variable permet de 
    $lesMois= getLesMois($mois);
    $lesCles = array_keys($lesMois);//
    $moisASelectionner = $lesCles[0];
    include  'vues/v_listesVisiteurEtMois.php';
    break;

case 'afficheFrais':
    $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $lesVisiteurs= $pdo->getLesVisiteurs();
    $levisiteurASelectionner= $idVisiteur;
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $lesMois= getLesMois($mois);
    $moisASelectionner= $leMois;
    $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
    if (!is_array($pdo->getLesInfosFicheFrais($idVisiteur, $leMois))) { 
        ajouterErreur('Pas de fiche de frais pour ce visiteur et ce mois');
        include 'vues/v_erreurs.php';
        include 'vues/v_listesVisiteurEtMois.php';
    } else {
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois); 
    $nbJustificatifs= $pdo->getNbjustificatifs($idVisiteur, $leMois);
    include 'vues/v_afficheFrais.php';
    }
    break;
    
 case 'corrigerFrais':
        $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);//on recupere les données de ce que le comptable a changer 
        $lesVisiteurs=$pdo->getLesVisiteurs();
        $levisiteurASelectionner=$idVisiteur;
        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $lesMois = getLesMois($mois);
        $moisASelectionner=$leMois;
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);//requette qui va prendre nos new donner
                echo "La modification a bien été prise en compte.";  
        } else {
                ajouterErreur('Les valeurs des frais doivent être numériques');
                include 'vues/v_erreurs.php';
               }
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);  
        include 'vues/v_afficheFrais.php';
        break;
    
 case 'corrigerFraisHF':
    $idVisiteur = filter_input(INPUT_POST, 'lstVisiteurs', FILTER_SANITIZE_STRING);
    $lesVisiteurs= $pdo->getLesVisiteurs();
    $levisiteurASelectionner= $idVisiteur;
    $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $lesMois= getLesMois($mois);
    $moisASelectionner= $leMois;
    $laDate = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $leMontant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
    $leLibelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
    $idFHF = filter_input(INPUT_POST, 'idFHF', FILTER_SANITIZE_NUMBER_INT);
    valideInfosFrais($laDate, $leLibelle, $leMontant);
    if (nbErreurs() != 0) {
        include 'vues/v_erreurs.php';
    } else {
        $pdo->MajFraisHorsForfait($idVisiteur,$leMois,$leLibelle,$laDate,$leMontant,$idFHF);
    }
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois); 
    $nbJustificatifs= $pdo->getNbjustificatifs($idVisiteur, $leMois);
    
    if (isset($_POST['corriger'])){
      $mois=getMoisSuivant($leMois);
       if ($pdo->estPremierFraisMois($idVisiteur, $mois)){
       $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
       }
       $pdo->majlibelle($idVisiteur,$leMois,$leLibelle,$idFHF);      
       $pdo->creeNouveauFraisHorsForfait($idVisiteur,$mois,$leLibelle,$laDate,$leMontant);
       
   }
    include 'vues/v_afficheFrais.php';
    break;
    
    
case 'validerFrais':
   $idVisiteur = filter_input(INPUT_POST, 'leVisiteur', FILTER_SANITIZE_STRING);
   $lesVisiteurs= $pdo->getLesVisiteurs();
   $leVisiteurASelectionner= $idVisiteur;
   $leMois = filter_input(INPUT_POST, 'leMois', FILTER_SANITIZE_STRING);
   $lesMois= getLesMois($mois);
   $moisASelectionner =$leMois;
   $etat="VA";
   $valideFrais=$pdo->majEtatFicheFrais($idVisiteur, $leMois, $etat);  
   $montantTotal=$pdo->montantTotal($idVisiteur,$leMois);
   $montantTotalHF=$pdo->montantTotalHorsF($idVisiteur,$leMois);
   
   if ($montantTotalHF[0][0]==null){//si il n y a pas de frais hors forfaits alors $montantTotalHF est=0
      $montantTotalHF=array();
      $montantTotalHF[0]=array(0);
   } 
   $pdo->majEtatFicheFrais($idVisiteur, $mois, $etat);
    ?>
    <div class="alert alert-info" role="alert">
    <p>La fiche a bien été validée!</p>
    </div>
    <?php
   include 'vues/v_listesVisiteurEtMois.php';
   break;
   
   
case 'supprimerFrais':
    $unIdFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_NUMBER_INT);
    $ceMois = filter_input(INPUT_GET, 'mois', FILTER_SANITIZE_STRING);

    $idVisiteur =filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    ?>
    <div class="alert alert-info" role="alert">
        <p><h4>Voulez vous modifier ou supprimer le frais?<br></h4>
        <a href="index.php?uc=validerFrais&action=supprimer&idFrais=<?php echo $unIdFrais ?>&mois=<?php echo $ceMois ?>">Supprimer</a> 
        ou <a href="index.php?uc=validerFrais&action=reporter&idFrais=<?php echo $unIdFrais ?>&mois=<?php echo $ceMois ?>&id=<?php echo $idVisiteur ?>">Reporter</a></p>
    </div>
    <?php
    break;

case 'supprimer':
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_NUMBER_INT);
    $pdo->refuserFraisHorsForfait($idFrais);
    ?>
    <div class="alert alert-info" role="alert">
        <p>Ce frais hors forfait a bien été supprimé!</p>
    </div>
    <?php
    break;

    include 'vues/v_retourAccueil.php';
    break;
    
 } 