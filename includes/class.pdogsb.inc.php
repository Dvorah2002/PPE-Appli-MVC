
<?php
/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
// propriété privée static= toujours pareil valeur ne change pas, qui stoke la bdd
// localhost= serveur local
//$= pour dire que c'est une variable.
    private static $bdd = 'dbname=gsb_frais';// dans la proriete bdd on met le nom de la bdd
    private static $user = 'userGsb';// contit=ent l'utilisateur
    private static $mdp = 'secret';//contient le mot de passe
    private static $monPdo;
    private static $monPdoGsb = null;//Cette propriété est nulle par défaut

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
       // méthode qui crée une instance de la classe Pdo. Chaque méthode est un objet de cette classe,
       //le constructeur sera exécuté donc à chaque fois qu'on déclare une méthode.
       // monPdo est dans la variable PdoGsb
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
        // ';'= concatenation des 2
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
        //monpdo de la classe pdogsbquery= requete sql
        //set= modifie charactere en utf8
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
// fonction qui ne changera pas, si mon pdogsb est egale a null alors je l'inastancie et je cree un objet  newpdogsb
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     *
     * @param String $login Login du visiteur
     * @param String $mdp   Mot de passe du visiteur
     *
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT visiteur.id AS id, visiteur.nom AS nom, '
            . 'visiteur.prenom AS prenom '
            . 'FROM visiteur '
            . 'WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp'
        );
// elle nous renvoi nom et prenom qui correspond au mdp et login
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
// facon de preparer la requete et que les parametres c'est ce qu'on a ecrit dans where
//fetch= ça lance
    }

    public function getInfosComptable($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT compta.id AS id, compta.nom AS nom, '
            . 'compta.prenom AS prenom '
            . 'FROM compta '
            . 'WHERE compta.login = :unLogin AND compta.mdp = :unMdp'
        );
// elle nous renvoi nom et prenom qui correspond au mdp et login
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
        // syntaxe pour commencer une requete sql
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois'
                //on va chercher un tablo et on verifie si c bien les memes
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        //fetch all= tout le resultat de la requte prepare
        for ($i = 0; $i < count($lesLignes); $i++) {
             $date = $lesLignes[$i]['date'];
            // on cherche dans l'index i le mot date
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
            // la ou y'a date on met la fonction dateAnglaisVersFrancais avec la variable date.
        }
        // cette fonction permet de convertir les dates de angais en francais
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
                //on selectionne le nbjustifi de fiche frais 
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
                //le visiteur egal a unid
            . 'AND fichefrais.mois = :unMois'
	    //Elles ont des point et des cotes.
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
	//retourne dans la ligne.
        return $laLigne['nb'];
	//cette methode est appalee a un 0enddroit. ds cette variable , elle va retourner la ligne nbr. c un tablo a lindice nbr .
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(//requette sql ; 
            'SELECT fraisforfait.id as idfrais, '
            . 'fraisforfait.libelle as libelle, '
            . 'lignefraisforfait.quantite as quantite '
            . 'FROM lignefraisforfait '
            . 'INNER JOIN fraisforfait '
            . 'ON fraisforfait.id = lignefraisforfait.idfraisforfait '
            . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraisforfait.mois = :unMois '
            . 'ORDER BY lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
	//fetchall:tablo # de fetch : lance sous forme de  tablo
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
            //elle est public car une autre class peu lutiliser
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
                //c'est une requete qui ns renvoie vers la base de donnée de pdo gsb
            'SELECT fraisforfait.id as idfrais '
                //on selectionne idfrais forfait et on renome en idfrais 
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
                //De frais forfait et organisé par les id de frais forfait
        );
        $requetePrepare->execute();
        //on execute 
        return $requetePrepare->fetchAll();
        //fetch all :retourne le resulat sous forme de tablo 
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
	//array keys :tablo de clé
        foreach ($lesCles as $unIdFrais) {
	//chaque ligne : les cle comme un idfrais 
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                    
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
	//elle va boucler sur les tablo les cles . chque ligne on va lapelr un idfrais. On va prendre cetet ligne et la rentrée ds la variable quantité. On crée un tablo les cle , avc esfrais. on 	 	parle d'un idfrais. Le resultat de chque ligne , on prend le resultat on met la quantité .Les frais c un ablo les cles st a lintereieur .. pr chque ligne la quantité et aprés on fait la 	requette SQL .Je la met ds le champs de la Qte de la BDD on met la valeur de quantité. Final ds la BDD il yaura le tablo lesfrais.  
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }
    
    /**
     * modifie les frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param float  $montant    Montant du frais
     *
     * @return null
     */

     public function majFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant,$idFrais) 
    {
       $dateFr = dateFrancaisVersAnglais($date);
       $requetePrepare = PdoGSB::$monPdo->prepare(       
                'UPDATE lignefraishorsforfait '
               . 'SET lignefraishorsforfait.date = :uneDateFr, '
               . 'lignefraishorsforfait.montant = :unMontant, '  
               . 'lignefraishorsforfait.libelle = :unLibelle '
               . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
               . 'AND lignefraishorsforfait.mois = :unMois '
               . 'AND lignefraishorsforfait.id = :unIdFrais'      
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
       $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
       $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
       $requetePrepare->execute();
       
   }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
	//C un changement du nbr de justificatif . On va changer le nbr de justificatif dans la basse de donné.
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
   * retourne le montant total des frais forfaitisés pour un visiteur et un mois donné
   * @param type $idVisiteur
   * @param type $mois
   * @return type
   */
   public function montantTotal($idVisiteur,$mois){
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT SUM(lignefraisforfait.quantite * fraisforfait.montant)'
           .'FROM lignefraisforfait JOIN fraisforfait ON (fraisforfait.id=lignefraisforfait.idfraisforfait)' 
           . 'WHERE lignefraisforfait.idvisiteur = :unIdVisiteur '
           . 'AND lignefraisforfait.mois = :unMois'    
       );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();      
   }
   
   /**
    * retourne le montant total des frais hors forfaits pour un visiteur et un mois donné
    * @param type $idVisiteur
    * @param type $mois
    * @return type
    */
   public function montantTotalHorsF($idVisiteur,$mois)
    {   
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT SUM(lignefraishorsforfait.montant )'
            .'FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND lignefraishorsforfait.mois = :unMois '  
            . 'AND lignefraishorsforfait.libelle NOT LIKE "REFUSE%" '
     );
       $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
       $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();
   }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :unMois '
            . 'AND fichefrais.idvisiteur = :unIdVisiteur'
        );
	//Si cette requette marche on retourne vrai , sinon nan. On regarde si la fiche frais existe pr ce mois si . 
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur'
        );
	//Elle va chercher le mois le plus haut de la fiche frais. Elle va nous retourner le resulatt ds la ligne. Dernier moi va donner le plus gr mois ds lequel il a saisi ce donné. 
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
	//CR: pas finis encore en cours 
	//if :condition
	//== : egalité # = : afectation {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
	    //CL  cloturer , dc le contable peu renter
        }
        
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (idvisiteur,mois,nbJustificatifs,'
            . 'montantValide,dateModif,idEtat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
	//variable qui sapl this 
        foreach ($lesIdFrais as $unIdFrais) {
	//foreach:chaque ligne
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (idvisiteur,mois,'
                . 'idFraisForfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
	
	//Elle met a jour une new pg en ligne pr les prochain mois 

        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
	//convertit la date du francais a angais  le resulatt renre ds la variable datefr

        $requetePrepare = PdoGSB::$monPdo->prepare(
	//on va interoger la bdd pdogsb, on rentre new valeur dans lignefraishorsforfait
            'INSERT INTO lignefraishorsforfait '
            . 'VALUES (null, :unIdVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
	//param idfrais
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY fichefrais.mois desc '
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        //c un tablo les mois
        while ($laLigne = $requetePrepare->fetch()) {
	//fetch: execute 
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
	    //substr : extraire 
            $numMois = substr($mois, 4, 2);
            $lesMois['$mois'] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }
    
     /**
     * Retourne les mois pour lesquels l'etat des fiches de frais est "VAlidée"
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisVA()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.idetat="VA" '
            . 'ORDER BY fichefrais.mois desc'
        );

        $requetePrepare->execute();
        
        $lesMois = array();       
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );       
        
        } 
        return $lesMois;
    }
    
	//on a recupere les mois de tt les client et on a fait d'un tablo , avc date , année et et mois.   }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        //chaine de caractére qui ns dis letat ou est le tablo 
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT ficheFrais.idEtat as idEtat, '
            . 'ficheFrais.dateModif as dateModif,'
            . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
            . 'ficheFrais.montantValide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN Etat ON ficheFrais.idEtat = Etat.id '
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idEtat = :unEtat, dateModif = now() '
	//fonction php qui va chercher ds la date actuelle. 
            . 'WHERE fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    public function getLesVisiteurs()
   {
       $requetePrepare = PdoGSB::$monPdo->prepare(
           'SELECT visiteur.id AS id,'
           . 'visiteur.nom as nom,'
           . 'visiteur.prenom AS prenom '
           . 'FROM visiteur'

       );
       $requetePrepare->execute();
       return $requetePrepare->fetchAll();//ca veux dire qu'on le retourne ds un tablo, le resulat est ds un tablo 
   }
   
   /**
     * Cree nouveau fhf dans la fiche du mois suivant dans le cas d'un report du frais hors forfait
     * @param string $idVisiteur    ID du visiteur
     * @param int $leMois           Mois sous la forme aaaamm
     * @param string $libelleHF     Libellé du frais
     * @param string $dateHF        Date du frais
     * @param int $montantHF        Montant du frais
     */
    public function creeFHFReporté($idVisiteur,$leMois,$libelleHF,$dateHF,$montantHF){
                    //var_dump($idVisiteur,$leMois,$libelleHF,$dateHF,$montantHF);
            $dateFr = dateFrancaisVersAnglais($dateHF);
            $requetePrepare = PdoGSB::$monPdo->prepare(
                "INSERT INTO lignefraishorsforfait "
                . "VALUES (null, :unIdVisiteur,:unMois,'$libelleHF', :uneDateFr,:unMontant)"
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);
            //$requetePrepare->bindParam(':unLibelle', $libelleHF, PDO::PARAM_STR);
            $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $montantHF, PDO::PARAM_INT);
            $requetePrepare->execute();
    }
    
    /**
     * Enlève le texte "REFUSE: " du libelle du fhf qui a été reporté
     * @param string $idVisiteur   ID du visiteur
     * @param int $leMois          Mois sous la forme aaaamm
     * @param string $libelleHF    Libellé du frais
     * @param string $dateHF       Date du frais
     * @param int $montantHF       Montant du frais
     */
    public function enleverTexteRefusé($idVisiteur,$leMois,$libelleHF,$dateHF,$montantHF){
        //var_dump($idVisiteur,$leMois,$libelleHF,$date,$montantHF);
        $dateFr = dateFrancaisVersAnglais($dateHF);
        $requetePrepare = PdoGSB::$monPdo->prepare(
                "UPDATE lignefraishorsforfait "
                . "SET libelle=SUBSTR('$libelleHF',8) "
                . "WHERE idvisiteur='$idVisiteur' "
                . "AND mois='$leMois' "
                . "AND date='$dateFr'"
                . "AND montant='$montantHF'"
            );    
        $requetePrepare->execute();
        /*$requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $leMois, PDO::PARAM_STR);  
        //$requetePrepare->bindParam(':unLibelle', $libelleHF, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montantHF, PDO::PARAM_INT);
        $requetePrepare->execute();*/
    /*    if($requetePrepare->execute()){
            echo "bien";
        }else{
            echo "pas bien";
        }*/
    }
     
    public function majlibelle($idVisiteur,$mois,$libelle,$idFHF)
   {
 
      $requetePrepare = PdoGSB::$monPdo->prepare(      
               'UPDATE lignefraishorsforfait '
              . 'SET lignefraishorsforfait.libelle = "REFUSE".:unLibelle '
              . 'WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur '
              . 'AND lignefraishorsforfait.mois = :unMois '
              . 'AND lignefraishorsforfait.id = :unIdFrais'  
              );
      $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
      $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);  
       $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_INT);
      $requetePrepare->execute();
     
  }

}

