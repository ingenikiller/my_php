<?php

class ContextExecution{
	
	public $m_objet;
	
	public $m_dataRequest;
	
	public $m_dataResponse=array();
	
	private $m_connexion=null;
	
	private $m_user=null;
	
	public $m_messages = null;
	public $m_erreurs = null;
	/**
	 * constructeur
	 */
	public function __construct(){
		$this->m_dataRequest = new DataRequest;
	}
	
	/**
	 * Cr�e la connexion � la base si elle n'existe pas
	 * 
	 */
	/*public function getConnexion(){
		if($this->m_connexion==null) {
			$this->m_connexion=new classe_bdd(HOST, USER, PASSWD, DATABASE);
			if (!$this->m_connexion->connect()) {
				die ('connexion impossible');
			}
		}
		return $this->m_connexion;
	}*/
	
	/**
	 * Acc�s � l'utilisateur identifi�
	 * @param unknown_type $p_user
	 */
	public function setUser($p_user){
		$this->m_user = $p_user;
	}
	public function getUser(){
		return $this->m_user;
	}
	
	/**
	 * Ajoute un message
	 * @param message � ajouter
	 */
	public function addMessage($p_message) {
		if($this->m_messages==NULL){
			$this->m_messages=array();
		}
		$this->m_messages[count($this->m_messages)] = $p_message;
	}
	
	public function addError($p_message) {
		if($this->m_erreurs==NULL){
			$this->m_erreurs=array();
		}
		$this->m_erreurs[count($this->m_erreurs)] = $p_message;
	}
	
	
	public function addDataBlockRow($p_blockRow) {
		$this->m_dataResponse[] = $p_blockRow;
	}
	
	
}


?>