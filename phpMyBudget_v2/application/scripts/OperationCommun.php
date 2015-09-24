<?php

/**
 * Description of OperationCommun
 *
 * @author ingeni
 * 
 */
class OperationCommun {
    
	/**
	 * Recherche une op�ration fille
	 * @param unknown_type $flux
	 * @param unknown_type $p_operation
	 */
	public static function rechercherOperationLiee($flux, $p_operation){
		$listOpe = new ListObject();
        $listOpe->name='ListeOperations';
        //recherche d'une op�ration ayant les r�f�rences de l'op�ration en cours
        $listOpe->request('Operation', "numeroCompteOri='$p_operation->noCompte' AND operationIdOri=$p_operation->operationId");
		$tab = $listOpe->tabResult;
        Logger::getInstance()->addLogMessage('Total ope '.count($tab));
        if($tab==null || count($tab)==0){
        	return null;
        } else {
        	return $tab[0];
        }
	}
	
	/**
     * Gestion d'une op�ration li�e pour les flux multi comptes
     */
    public static function operationLiee($p_operation){
        //recherche de la conf du flux
        $l_flux = new Flux();
        $l_flux->fluxId = $p_operation->fluxId;
        $l_flux->load();
        //
        Logger::getInstance()->addLogMessage('Operationid '.$p_operation->operationId);

        //si le flux n'a pas de compte li�
        if($l_flux->compteDest =='') {
            //on recherche une op�ration li�e pour la supprimer ( cas du changement de flux)
        	$operation = self::rechercherOperationLiee($l_flux, $p_operation);
        	if($operation!=null){
        		$operation->delete();
        	}
            return true;
        } else {
            //le flux a un compte destination
            //ce compte est diff�rent du compte en cours
        	if($l_flux->compteDest != $p_operation->noCompte) {
                Logger::getInstance()->addLogMessage('Operation liee');
                //recherche d'une op�arion existante
                $listOpe = new ListObject();
                $listOpe->name='ListeOperations';
                $listOpe->request('Operation', "operationIdOri=$p_operation->operationId");

                $tab = $listOpe->tabResult;
                Logger::getInstance()->addLogMessage('Total ope '.count($tab));
				//pas d'op�ration, on en cr�e une nouvelle
                if($tab==null || count($tab)==0) {
                    Logger::getInstance()->addLogMessage('Operation inexistante');
                    $l_operation = new Operation();
                    $l_operation->libelle = $p_operation->libelle;
                    $l_operation->date = $p_operation->date;
                    $l_operation->noCompte = $l_flux->compteDest;
                    $l_operation->fluxId = $p_operation->fluxId;
                    $l_operation->modePaiementId = $p_operation->modePaiementId;
                    $l_operation->montant = - $p_operation->montant;
                    $l_operation->verif = $p_operation->verif;
                    $l_operation->operationIdOri = $p_operation->operationId;
                    $l_operation->numeroCompteOri =  $p_operation->noCompte;
                    $l_operation->create();
                } else {
                    //operation existante
                    Logger::getInstance()->addLogMessage('Operation existante');
                    Logger::getInstance()->addLogMessage($p_operation->noCompte."----".$l_flux->compteDest);
                    //si l'op�ration est sur le compte destination, mise � jour
                    if($p_operation->noCompte==$l_flux->compteDest){
	                    Logger::getInstance()->addLogMessage('Operation meme compte');
	                    $l_operation = $tab[0];
	                    $l_operation->libelle = $p_operation->libelle;
	                    $l_operation->modePaiementId = $p_operation->modePaiementId;
	                    $l_operation->montant = - $p_operation->montant;
	                    $l_operation->verif = $p_operation->verif;
	                    $l_operation->update();
                    } else {
                    	//sinon, on modifie le num�ro de compte
                    	Logger::getInstance()->addLogMessage('Operation compte different');
                    	$l_operation = $tab[0];
	                    $l_operation->libelle = $p_operation->libelle;
	                    $l_operation->modePaiementId = $p_operation->modePaiementId;
	                    $l_operation->noCompte=$l_flux->compteDest;
	                    $l_operation->montant = - $p_operation->montant;
	                    $l_operation->verif = $p_operation->verif;
	                    $l_operation->update();
                    }
                }
            } else {
                if($l_flux->compteDest == $p_operation->noCompte){
                    //mise � jour de l'op�ration d'origine
                    $listOpeRec = new ListObject();
                    $listOpe->request('Operation', "noCompte='$l_flux->compteDest' AND noCompte='$p_operation->numeroCompteOri' AND operationId=$p_operation->operationIdOri");
                    $tab = $listOpeRec->tabResult;
                    Logger::getInstance()->addLogMessage('Total ope '.count($tab));
                    if($tab==null || count($tab)==0) {
                        $l_operation = $tab[0];
                        $l_operation->libelle = $p_operation->libelle;
                        $l_operation->modePaiementId = $p_operation->modePaiementId;
                        $l_operation->montant = - $p_operation->montant;
                        $l_operation->verif = $p_operation->verif;
                        $l_operation->update();
                    }
                } else {
                    return true;
                }
            }
        }    
    }
}
?>