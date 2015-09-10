<?php

//fonction __autoload "redéfinie"
function __autoload($p_className) {
	ClassAutoLoader::chargeClasse($p_className);
}

//chemin du fichier de configuration
define( 'CMEMIN_CLASSLOADER', './config/configLoader.xml', true);

class ClassAutoLoader {

	//instance de la classe
	private static $m_instance=null;
	
	//tableau contenant les chemins des classes
	private $m_tabChemins=null;
	
	/**
	 *constructeur
	 */
	private function __construct() {
		if(!is_file(CMEMIN_CLASSLOADER)) {
			die ('Erreur grave: chemin ' . CMEMIN_CLASSLOADER . 'xml introuvable');
		}
		$this->m_tabChemins = simplexml_load_file(CMEMIN_CLASSLOADER);
		if($this->m_tabChemins == null) {
			die ('Erreur grave: vérifier la structure du fichier de configuration '. CMEMIN_CLASSLOADER);
		}
	}
	
	/**
	 *retourne  l'instance de la classe
	 */
	private static function getInstance() {
		if(self::$m_instance == null) {
			self::$m_instance = new ClassAutoLoader();
		}
		return self::$m_instance;
	}
	
	/**
	 * Charge la classe dont le nom est passé en paramètre
	 *
	 *@param nom de la classe
	 */
	public static function chargeClasse($p_className) {
		$l_instance = self::getInstance();
		$l_instance->rechercheClasse($p_className);		
	}
	
	/**
	 * charge la classe dont le nom est passé en paramètre. zffectue le require_once
	 * pas de traitement d'erreur si la classe n'est pas trouvée: l'erreur doit être levée lors des devs
	 *
	 *@param nom de la classe
	 */
	private function rechercheClasse($p_className) {
		//recherche dans le tableau des chemins
		foreach($this->m_tabChemins as $l_key=>$l_chemin) {
			
			//si le fichier est trouvé dans le répertoire en cours
			if(is_file("$l_chemin/$p_className.php")) {
				require_once "$l_chemin/$p_className.php";
			}
		}
		
	}
	
} //fermeture classe ClassAutoLoader
?>