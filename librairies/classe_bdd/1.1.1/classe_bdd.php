<?php

/**************************************************************
 * Created on 18 mai 2005
 *
 * classe_bdd.php
 * 
 * path: classes/
 *
 **************************************************************
 * version 1.0 au 12/06/2005
 *  
 **************************************************************
 * version 1.1 au 11/09/2005
 *  utilisation de la classe mysqli
 *
 *19/09/2005: -correction bug affichage si serveur offline
 *            -suppresion méthodes redéfinies inutilement
 *            -suppression d'attributs de classe inutiles
 *04/01/2005: -ajout methodes nbRows et fetchArray
 **************************************************************
 * version 1.1 au 11/09/2005
 *  utilisation de la classe mysqli
 *
 *19/09/2005: -correction bug affichage si serveur offline
 *            -suppresion méthodes redéfinies inutilement
 *            -suppression d'attributs de classe inutiles
 *04/01/2005: -ajout methodes nbRows et fetchArray
 **************************************************************/

 
 
class classe_bdd extends mysqli
{
	/*****************************************************************************
	 *fonction __construct
	 * constructeur de la classe
	 *
	 * paramètres:
	 *	- p_host: serveur
	 *	- p_user: nom de connexion
	 *	- p__password: mot de passe de connexion
	 *	- p_db: base de donnees
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	public function __construct($p_host, $p_user, $p_password, $p_db)
	{
		$this->l_connectOK = 0;
		//constructeur de la classe mère
		@parent::__construct($p_host, $p_user, $p_password, $p_db);
        //le commit est automatique
		@parent::autocommit(1);
	}
	
	/*****************************************************************************
	 *fonction __destruct
	 * destructeur de la classe
	 * coupe la connexion à la bdd si elle existe
	 *
	 * paramètres:
	 *	- rien
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	public function __destruct()
	{
		//si connecté
		if($this->l_connectOK)
		{
			parent::close();
		}
	}
	
	/*****************************************************************************
	 *fonction _connect
	 * connexion à la base de donnees
	 *
	 * paramètres:
	 *	- rien
	 * retourne:
	 *	- 0: connexion à la bdd impossible
	 *	- 1: connexion effectuée
	 *****************************************************************************/
	public function connect()
	{
		//connexion au serveur
		if (mysqli_connect_error())
		{
			$this->logErreur("Connexion impossible", mysqli_connect_error());
			$this->m_lastError = mysqli_connect_error();
			return 0;
		}
		else
		{
			//si aucun probleme
			$this->l_connectOK = 1;
		
		}
		return 1;
	}

	/*****************************************************************************
	 *fonction autocommit
	 * destructeur de la classe
	 * coupe la connexion à la bdd si elle existe
	 *
	 * paramètres:
	 *	- booleen 
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	public function autocommit(boolean $p_etat)
	{
		@parent::autocommit($p_etat);
	}

	/*****************************************************************************
	 *fonction requeteBDD
	 * paramètres:
	 *	- p_requete: requete à éxécuter
	 *	- p_message: erreur SQL retournée
	 *	- p__result: resultat de la requete
	 * retourne:
	 *	- 0 si erreur SQL
	 *	- 1 si aucune erreur SQL
	 *****************************************************************************/
	public function requeteBDD($p_requete)
	{
		//execute la requete
		$l_result = parent::query($p_requete);
		
		//teste si il y a eu une erreur
		if ($l_result == FALSE)
		{
			$this->logErreur($this->error, $p_requete);
			$this->m_lastError = $this->error;
			return 0;
		}
		
		return $l_result;
	}

	/*****************************************************************************
	 *fonction nbRows
	 * paramètres:
	 *	- p_result: resultat de la requete
	 * retourne:
	 *	- nombre d'enregistrement
	 *****************************************************************************/
	public function nbRows($p_result)
	{
		return $p_result->num_rows;
	}

	/*****************************************************************************
	 *fonction fetchArray
	 * paramètres:
	 *	- p_result: resultat de la requete
	 * retourne:
	 *	- extraction d'un enregistrement sous forme de tableau associatif
	 *****************************************************************************/
	public function fetchArray($p_result)
	{
		return $p_result->fetch_assoc();
	}

    /*****************************************************************************
	 *fonction fetchRow
	 * paramètres:
	 *	- p_result: resultat de la requete
	 * retourne:
	 *	- extraction d'un enregistrement d'un tableau
	 *****************************************************************************/
	public function fetchRow($p_result)
	{
		return $p_result->fetch_row();
	}

	/*****************************************************************************
	 *fonction last_insert_id
	 * paramètres:
	 *	- acun
	 * retourne:
	 *	- extraction d'un enregistrement
	 *****************************************************************************/
	public function last_insert_id()
	{
		return parent::insert_id;
	}


	/*****************************************************************************
	 *fonction logErreur
	 * paramètres:
	 *	- p_erreur: erreur SQL retournée
	 *	- p_requete: resultat de la requete
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	private function logErreur($p_erreur, $p_requete)
	{
		//ouverture fichier
		$fichier = fopen(CHEMIN_LOGERREUR . "log_sql.txt", "a");
		
		//ecrit erreur
		fwrite($fichier, "[" . date("d-m-Y H-i") . "]: "  . $p_erreur . ": " . $p_requete . "\n");
		
		//fermeture fichier
		fclose($fichier);
	}

	/*****************************************************************************
	 *fonction getLastError
	 * paramètres:
	 *	- p__result: resultat de la requete
	 * retourne:
	 *	- nombre d'enregistrement
	 *****************************************************************************/
	public function getLastError()
	{
		return $this->m_lastError;
	}

	//booleen de verification de connexion
	//vérifié dans le destructeur de la classe qui ferme la connexion à la base
	private $l_connectOK;
	private $m_lastError;
}

?>
