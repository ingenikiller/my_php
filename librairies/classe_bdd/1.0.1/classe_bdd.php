<?php

/************************************************************************
 * Created on 18 mai 2005
 *
 * classe_bdd.php
 *
 * path: classes/
 *
 * Classe servant � rediriger les m�thodes mysql
 *
 ************************************************************************
 * version 1 au 12/06/2005
 ************************************************************************
 * version au 23/12/2005
 * gestion de fonctions mysql
 * suppl�mentaires
 ************************************************************************/



class classe_bdd
{
	/*****************************************************************************
	 *fonction __construct
	 * constructeur de la classe
	 *
	 * param�tres:
	 *	- p_host: serveur
	 *	- p_user: nom de connexion
	 *	- p__password: mot de passe de connexion
	 *	- p_db: base de donnees
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	public function __construct($p_host, $p_user, $p_password, $p_db)
	{
		$this->host = $p_host;
		$this->user = $p_user;
		$this->password = $p_password;
		$this->db = $p_db;
		$this->l_connectOK = 0;
		$this->m_lastError = '';
	}

	/*****************************************************************************
	 *fonction __destruct
	 * destructeur de la classe
	 * coupe la connexion � la bdd si elle existe
	 *
	 * param�tres:
	 *	- rien
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	public function __destruct()
	{
		//si connect�
		if($this->l_connectOK)
		{
			//mysql_close();
		}
	}

	/*****************************************************************************
	 *fonction _connect
	 * connexion � la base de donnees
	 *
	 * param�tres:
	 *	- rien
	 * retourne:
	 *	- 0: connexion � la bdd impossible
	 *	- 1: connexion effectu�e
	 *****************************************************************************/
	public function connect()
	{
		//connexion au serveur
		$controle = @mysql_pconnect($this->host, $this->user, $this->password);
		if (!$controle)
		{
			$this->logErreur("Connexion impossible", mysql_error());
			return 0;
		}

		//selection de la base
		$controle = @mysql_select_db($this->db);
		if (!$controle)
		{
			$this->logErreur("Connexion impossible", mysql_error());
			return 0;
		}

		//si aucun probleme
		$this->l_connectOK = 1;
		return 1;
	}

	/*****************************************************************************
	 *fonction requeteBDD
	 * param�tres:
	 *	- p_requete: requete � �x�cuter
	 * retourne:
	 *	- resultat de la requete ou FALSE si erreur
	 *****************************************************************************/
	public function requeteBDD($p_requete)
	{
		//execute la requete
		$l_result = mysql_query($p_requete);
		
		//teste si il y a eu une erreur
		if ($l_result == FALSE)
		{
            $l_message = mysql_error();
            $this->m_lastError = $l_message;
			$this->logErreur( $l_message, $p_requete );
		}
		return $l_result;
	}
	
	/*****************************************************************************
	 *fonction last_insert_id
	 * param�tres:
	 * retourne:
	 *	- retourne le dernier identifiant g�n�r�
	 *****************************************************************************/
	public function last_insert_id()
	{
		return mysql_insert_id();
	}
	
    /*****************************************************************************
	 *fonction getLastError
	 * param�tres:
	 *	- p__result: resultat de la requete
	 * retourne:
	 *	- nombre d'enregistrement
	 *****************************************************************************/
	public function getLastError()
	{
		return $this->m_lastError;
	}


	/*****************************************************************************
	 *fonction nbRows
	 * param�tres:
	 *	- p__result: resultat de la requete
	 * retourne:
	 *	- nombre d'enregistrement
	 *****************************************************************************/
	public function nbRows($p_result)
	{
		return @mysql_num_rows($p_result);
	}

	/*****************************************************************************
	 *fonction fetchArray
	 * param�tres:
	 *	- p__result: resultat de la requete
	 * retourne:
	 *	- nombre d'enregistrement
	 *****************************************************************************/
	public function fetchArray($p_result)
	{
		return @mysql_fetch_array($p_result);
	}


	public static function mysqlFetchArray($p_result) {
		return @mysql_fetch_array($p_result);
	}


	/*****************************************************************************
	 *fonction logErreur
	 * param�tres:
	 *	- p_erreur: erreur SQL retourn�e
	 *	- p_requete: resultat de la requete
	 * retourne:
	 *	- rien
	 *****************************************************************************/
	private function logErreur($p_erreur, $p_requete)
	{
		//ouverture fichier
		$l_fichier = fopen(CHEMIN_LOGERREUR . "log_sql.txt", "a");

		//ecrit erreur
		fwrite($l_fichier, "[" . date("d-m-Y H:i") . "]: "  . $p_erreur . ": " . $p_requete . "\n");

		//fermeture fichier
		fclose($l_fichier);
	}



	//variables de connexion � la base
	private $host;
	private $user;
	private $password;
	private $db;

	//booleen de verification de connexion
	//v�rifi� dans le destructeur de la classe qui ferme la connexion � la base
	private $l_connectOK;
	private $m_lastError;

}

?>
