<?php

/**
 * Controleur Connexion
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Dvorah Simha Touati
 * @author    Beth Sefer
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
//action on filtre 
if (!$action) {
    $action = 'demandeconnexion';
    //on verifie la valeur de action
}

switch ($action) {
    //on se redirige vers une autre page 
case 'demandeConnexion':
    include 'vues/v_connexion.php';
    break;
case 'valideConnexion':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);
    $visiteur = $pdo->getInfosVisiteur($login, $mdp);
    $comptable =$pdo->getInfosComptable($login,$mdp);
    //va ds la case pdo et je lance le login et mdp 
    if (!is_array($visiteur) && !is_array($comptable)) {
        //is array c'est un tablo
        //si ya pas de login et mdp alors on lui dis de recomencer
        ajouterErreur('Login ou mot de passe incorrect');
        include 'vues/v_erreurs.php';
        include 'vues/v_connexion.php';
} else{ if (is_array($visiteur)){
        //si ca marche 
        $id = $visiteur['id'];
        $nom = $visiteur['nom'];
        //rentre ds le tablo visiteur , on prend les donnée et on les transfere dans des variables(id , nom , prenom)
        $prenom = $visiteur['prenom'];
        $statut = 'visiteur';
      
    }else if (is_array($comptable)){
        $id=$comptable['id'];
        $nom = $comptable['nom'];
        $prenom = $comptable['prenom'];
        $statut = 'comptable';
    }
    connecter($id, $nom, $prenom, $statut);
        //on envoie en parmetre les  3 variables quon veint de recuperer 
        header('Location: index.php');
}
    break;
default:
    include 'vues/v_connexion.php';
    break;
}
