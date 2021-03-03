<?php
/**
 * Gestion de l'affichage des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Dvorah Simha Touati
 * @author    Beth Sefer
 */

$estComptableconnecte= estComptableConnecte();
$estVisiteurconnecte= estVisiteurConnecte();

if ($estComptableconnecte) {
  include 'vues/v_acceuil_comptable.php';// on est redirigé vers la vue accueil
  
} else if ($estVisiteurconnecte) {
  include 'vues/v_accueil_visiteur.php';// on est redirigé vers la vue accueil
}else {
// si elle est vide
  include 'vues/v_connexion.php';// on va vers vue connexion
}