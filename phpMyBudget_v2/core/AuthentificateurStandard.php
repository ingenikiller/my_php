<?php

class AuthentificateurStandard {

	public function __construct() {
	
	}

	/*public function getLoginSession() {
		if(isset($_SESSION['login'])) {
			return $_SESSION['login'];
		}
		
		return null;;
	}*/
	
	public function authenticate($p_contexte){
		Logger::getInstance()->addLogMessage('appel authenticate'. ' avec ' . $_SESSION['userid']);

		$user = new Users();
		$user->userId = $_SESSION['userid'];
		$user->load();
		$p_contexte->setUser($user);
	}
}

?>