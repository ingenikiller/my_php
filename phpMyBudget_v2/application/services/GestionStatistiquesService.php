<?php

class GestionStatistiquesService extends ServiceStub {
	
	/*************************************************************/
	//
	// gestion des relev�s
	//
	/*************************************************************/
	
	public function affFormReleves(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        $listReleves = new ListDynamicObject();
        $listReleves->name = 'ListeReleves';
        $listReleves->request("select distinct noreleve from operation where nocompte='$numeroCompte' order by noreleve DESC");
        $p_contexte->addDataBlockRow($listReleves);
    }
	
	public function statReleves(ContextExecution $p_contexte){
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        
        
        /*$requeteAsso = 'SELECT SUM( montant) AS total , fluxId
					FROM operation 
					WHERE nocompte='.$numeroCompte.' and noReleve=\'$parent->noreleve\' GROUP BY fluxid';*/
        $requeteAsso = 'SELECT total, fluxId
					FROM stat_flux_releve
					WHERE nocompte=' . $numeroCompte . ' and noreleve = \'$parent->noreleve\' GROUP BY fluxid';
        $listMontantFlux= new ListDynamicObject();
        $listMontantFlux->name='ListeMontantFlux';
        $listMontantFlux->setAssociatedRequest(null, $requeteAsso);
        
        $premierReleve = $p_contexte->m_dataRequest->getData('premierReleve');
        $dernierReleve = $p_contexte->m_dataRequest->getData('dernierReleve');
        $l_requete = "SELECT distinct noreleve FROM operation WHERE nocompte='$numeroCompte'";
        if($dernierReleve=='') {
            $dernierReleve=$premierReleve;
        }
        $l_requete.=" AND noreleve between '$premierReleve' AND '$dernierReleve' order by noreleve ASC";
        
        //requete de calcul des totaux cr�dit
        $requeteCredit = 'SELECT SUM( montant) AS total, noReleve
					FROM operation 
					WHERE nocompte='.$numeroCompte.' and noReleve=\'$parent->noreleve\' and montant > 0 GROUP BY noReleve';
        $totalCredit= new ListDynamicObject();
        $totalCredit->name='TotalCredit';
        $totalCredit->setAssociatedRequest(null, $requeteCredit);
        
        //requete de calcul des totaux d�bit
        $requeteDebit = 'SELECT SUM( montant) AS total, noReleve
					FROM operation 
					WHERE nocompte='.$numeroCompte.' and noReleve=\'$parent->noreleve\' and montant < 0 GROUP BY noReleve';
        $totalDebit= new ListDynamicObject();
        $totalDebit->name='TotalDebit';
        $totalDebit->setAssociatedRequest(null, $requeteDebit);
        
        $listeReleves = new ListDynamicObject();
        $listeReleves->name='ListeReleves';
        $listeReleves->setAssociatedKey($listMontantFlux);
        $listeReleves->setAssociatedKey($totalCredit);
        $listeReleves->setAssociatedKey($totalDebit);
        $listeReleves->request($l_requete);
        $p_contexte->addDataBlockRow($listeReleves);
        
		
		$requeteMontantFils='SELECT sum(montant) AS total, noreleve, fluxId
						FROM operation 
						WHERE operation.nocompte=\''.$numeroCompte.'\' and fluxId=$parent->fluxId
						AND noreleve  between \''.$premierReleve.'\' and \''.$dernierReleve.'\' group by substr(date, 1, 7)';
		$montantFluxFils = new ListDynamicObject();
		$montantFluxFils->name='MontantFluxFils';
		$montantFluxFils->setAssociatedRequest(null, $requeteMontantFils);
		
		
		//flux fils
		$requeteFlux = "SELECT fluxId, flux FROM flux WHERE compteId='$numeroCompte'". ' AND fluxMaitreId=$parent->fluxId ORDER BY flux';
		$listFluxFils = new ListDynamicObject();
		$listFluxFils->name = 'ListeFluxFils';
		$listFluxFils->setAssociatedKey($montantFluxFils);
        $listFluxFils->setAssociatedRequest(null, $requeteFlux);
		
		
		
        $listeFlux = new ListDynamicObject();
        $listeFlux->name='ListeFlux';
		$listeFlux->setAssociatedKey($listFluxFils);
        /*$listeFlux->request("SELECT DISTINCT flux.fluxId, flux FROM operation 
						LEFT JOIN flux ON flux.fluxId = operation.fluxId 
						WHERE  noreleve BETWEEN '$premierReleve' and '$dernierReleve'
                                                AND nocompte='$numeroCompte'
						ORDER BY flux ASC, flux ASC");*/
		$listeFlux->request("SELECT DISTINCT flux.fluxId, flux, operationRecurrente , flux.fluxMaitre FROM stat_flux_releve
						LEFT JOIN flux ON flux.fluxId = stat_flux_releve.fluxId 
                                                WHERE noreleve between '$premierReleve' and '$dernierReleve' and nocompte='$numeroCompte' ORDER BY flux");
        $p_contexte->addDataBlockRow($listeFlux);
    }
	
	/*************************************************************/
	//
	// gestion des mois
	//
	/*************************************************************/
	
	public function affFormAnnees(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        $listReleves = new ListDynamicObject();
        $listReleves->name = 'ListeAnnee';
        $listReleves->request("SELECT DISTINCT substr( date, 1, 4 ) as annee FROM operation WHERE nocompte = '$numeroCompte' order by annee asc");
        $p_contexte->addDataBlockRow($listReleves);
    }
	
	public function statMois(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');

        //requ�te des montants par flux/mois
        $requeteAsso = 'SELECT total, fluxId
					FROM stat_flux 
					WHERE nocompte=' . $numeroCompte . ' and mois like concat(\'$parent->mois\',\'%\') GROUP BY fluxid';

        $listMontantFlux = new ListDynamicObject();
        $listMontantFlux->name = 'ListeMontantFlux';
        $listMontantFlux->setAssociatedRequest(null, $requeteAsso);
        
        //requ�te des op�rations r�currentes
        $requeteTotaux = "SELECT sum(montant) AS total
						FROM operation 
						LEFT JOIN flux ON flux.fluxId = operation.fluxId  
						WHERE operation.nocompte='$numeroCompte' and operationRecurrente='checked'" .
                'AND date like concat(\'$parent->mois\',\'%\')';
        $listMontantTotaux = new ListDynamicObject();
        $listMontantTotaux->name = 'ListeMontantOpeRecurrente';
        $listMontantTotaux->setAssociatedRequest(null, $requeteTotaux);

        //requ�te des calculs concernant l'�pargne
        $requeteEpargne = "SELECT sum(montant) AS total
						FROM operation 
						LEFT JOIN flux ON flux.fluxId = operation.fluxId  
						WHERE operation.nocompte='$numeroCompte' and entreeEpargne='checked'" .
                'AND date like concat(\'$parent->mois\',\'%\')';
        $listMontantEpargne = new ListDynamicObject();
        $listMontantEpargne->name = 'ListeMontantEpargne';
        $listMontantEpargne->setAssociatedRequest(null, $requeteEpargne);

        $premierMois = $p_contexte->m_dataRequest->getData('premiereAnnee') . '-' . $p_contexte->m_dataRequest->getData('premierMois') . '-01';
        $dernierReleve = '';
        if ($p_contexte->m_dataRequest->getData('derniereAnnee') == null) {
            $dernierReleve = $p_contexte->m_dataRequest->getData('premiereAnnee') . '-' . $p_contexte->m_dataRequest->getData('premierMois') . '-31';
        } else {
            $dernierReleve = $p_contexte->m_dataRequest->getData('derniereAnnee') . '-' . $p_contexte->m_dataRequest->getData('dernierMois') . '-31';
        }
        $p_contexte->m_dataRequest->getData('dernierReleve');
        //requ�te principale
        $l_requete = "SELECT distinct substr(date,1,7) AS mois FROM operation WHERE date between '$premierMois' and '$dernierReleve' and nocompte='$numeroCompte' order by mois";

        $listeReleves = new ListDynamicObject();
        $listeReleves->name = 'ListeMois';
        $listeReleves->setAssociatedKey($listMontantFlux);
        $listeReleves->setAssociatedKey($listMontantTotaux);
        $listeReleves->setAssociatedKey($listMontantEpargne);
        $listeReleves->request($l_requete);
        $p_contexte->addDataBlockRow($listeReleves);

		
		$requeteMontantFils='SELECT sum(montant) AS total, substr(date, 1, 7) as date, fluxId
						FROM operation 
						WHERE operation.nocompte=\''.$numeroCompte.'\' and fluxId=$parent->fluxId
						AND date  between \''.$premierMois.'\' and \''.$dernierReleve.'\' group by substr(date, 1, 7)';
		$montantFluxFils = new ListDynamicObject();
		$montantFluxFils->name='MontantFluxFils';
		$montantFluxFils->setAssociatedRequest(null, $requeteMontantFils);
		
		
		//flux fils
		$requeteFlux = "SELECT fluxId, flux FROM flux WHERE compteId='$numeroCompte'". ' AND fluxMaitreId=$parent->fluxId ORDER BY flux';
		$listFluxFils = new ListDynamicObject();
		$listFluxFils->name = 'ListeFluxFils';
		$listFluxFils->setAssociatedKey($montantFluxFils);
        $listFluxFils->setAssociatedRequest(null, $requeteFlux);
		
		
        //liste des flux
        $listeFlux = new ListDynamicObject();
        $listeFlux->name = 'ListeFlux';
		$listeFlux->setAssociatedKey($listFluxFils);
        $listeFlux->request("SELECT DISTINCT flux.fluxId, flux, operationRecurrente , flux.fluxMaitre FROM stat_flux 
						LEFT JOIN flux ON flux.fluxId = stat_flux.fluxId 
                                                WHERE concat(mois, '-15') between '$premierMois' and '$dernierReleve' and nocompte='$numeroCompte' ORDER BY flux");
        $p_contexte->addDataBlockRow($listeFlux);
    }
	
	/*************************************************************/
	//
	// gestion des mois
	//
	/*************************************************************/
	
	public function affFormMois(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        $listReleves = new ListDynamicObject();
        $listReleves->name = 'ListeAnnee';
        $listReleves->request("SELECT DISTINCT substr( date, 1, 4 ) as annee FROM operation WHERE nocompte = '$numeroCompte' order by annee asc");
        $p_contexte->addDataBlockRow($listReleves);
    }
	
	public function statAnnees(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        //requ�te des montants par flux/mois
        /*$requeteAsso = 'SELECT SUM( montant) AS total , fluxId, \'$parent->annee\' AS periode
					FROM operation 
					WHERE nocompte=' . $numeroCompte . ' and date like concat(\'$parent->annee\',\'%\') GROUP BY fluxid';*/
        $requeteAsso = 'SELECT fluxId, substr(mois, 1, 4 ) AS periode, fluxMaitre, sum(total) as total
					FROM stat_flux
					WHERE nocompte=\'' . $numeroCompte . '\' and mois like concat(\'$parent->annee\',\'%\') GROUP BY fluxid';
					
        $listMontantFlux = new ListDynamicObject();
        $listMontantFlux->name = 'ListeMontantFlux';
        $listMontantFlux->setAssociatedRequest(null, $requeteAsso);

			
		
		
		
		
        //requ�te des op�rations r�currentes
        $requeteTotaux = "SELECT sum(montant) AS total
						FROM operation 
						LEFT JOIN flux ON flux.fluxId = operation.fluxId  
						WHERE operation.nocompte='$numeroCompte' and operationRecurrente='checked'" .
                'AND date like concat(\'$parent->annee\',\'%\')';
        $listMontantTotaux = new ListDynamicObject();
        $listMontantTotaux->name = 'ListeMontantOpeRecurrente';
        $listMontantTotaux->setAssociatedRequest(null, $requeteTotaux);

        //requ�te des calculs concernant l'�pargne
        $requeteEpargne = "SELECT sum(montant) AS total
						FROM operation 
						LEFT JOIN flux ON flux.fluxId = operation.fluxId  
						WHERE operation.nocompte='$numeroCompte' and entreeEpargne='checked'" .
                'AND date like concat(\'$parent->annee\',\'%\')';
        $listMontantEpargne = new ListDynamicObject();
        $listMontantEpargne->name = 'ListeMontantEpargne';
        $listMontantEpargne->setAssociatedRequest(null, $requeteEpargne);
        
        $premiereAnnee = $p_contexte->m_dataRequest->getData('premiereAnnee');
        $derniereAnnee = '';
        if ($p_contexte->m_dataRequest->getData('derniereAnnee') == null) {
            $derniereAnnee = $premiereAnnee.'-12-31';
        } else {
            $derniereAnnee = $p_contexte->m_dataRequest->getData('derniereAnnee').'-12-31';
        }
        $premiereAnnee.='-01-01';
        //$p_contexte->m_dataRequest->getData('dernierReleve');
        //requ�te principale
        $l_requete = "SELECT distinct substr(date,1,4) AS annee FROM operation WHERE date between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' order by annee";

        $listeReleves = new ListDynamicObject();
        $listeReleves->name = 'ListeAnnees';
        $listeReleves->setAssociatedKey($listMontantFlux);
        $listeReleves->setAssociatedKey($listMontantTotaux);
        $listeReleves->setAssociatedKey($listMontantEpargne);
        $listeReleves->request($l_requete);
        $p_contexte->addDataBlockRow($listeReleves);

		
		$requeteMontantFils='SELECT sum(montant) AS total, substr(date, 1, 4) as date, fluxId
						FROM operation 
						WHERE operation.nocompte=\''.$numeroCompte.'\' and fluxId=$parent->fluxId
						AND date  between \''.$premiereAnnee.'\' and \''.$derniereAnnee.'\' group by substr(date, 1, 4)';
		$montantFluxFils = new ListDynamicObject();
		$montantFluxFils->name='MontantFluxFils';
		$montantFluxFils->setAssociatedRequest(null, $requeteMontantFils);
		
		
		//flux fils
		$requeteFlux = "SELECT fluxId, flux FROM flux WHERE compteId='$numeroCompte'". ' AND fluxMaitreId=$parent->fluxId ORDER BY flux';
		$listFluxFils = new ListDynamicObject();
		$listFluxFils->name = 'ListeFluxFils';
		$listFluxFils->setAssociatedKey($montantFluxFils);
        $listFluxFils->setAssociatedRequest(null, $requeteFlux);
		
        //liste des flux
        $listeFlux = new ListDynamicObject();
        $listeFlux->name = 'ListeFlux';
		$listeFlux->setAssociatedKey($listFluxFils);
        $listeFlux->request("SELECT DISTINCT flux.fluxId, flux, operationRecurrente , flux.fluxMaitre FROM stat_flux 
						LEFT JOIN flux ON flux.fluxId = stat_flux.fluxId 
                                                WHERE concat(mois, '-15') between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' ORDER BY flux");
                                                //WHERE date between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' ORDER BY flux");
        $p_contexte->addDataBlockRow($listeFlux);
        
    }

    public function statFlux(ContextExecution $p_contexte) {
        $numeroCompte = $p_contexte->m_dataRequest->getData('numeroCompte');
        
        $fluxSelect = $p_contexte->m_dataRequest->getData('listeflux');
        Logger::getInstance()->addLogMessage('liste flux:' . $fluxSelect);
        $fluxSelect = "'".str_replace('|', "','", $fluxSelect)."'";
        Logger::getInstance()->addLogMessage('liste flux:' . $fluxSelect);

        $requeteAsso = 'SELECT fluxId, substr(mois, 1, 4 ) AS periode, fluxMaitre, sum(total) as total
                    FROM stat_flux
                    WHERE nocompte=\'' . $numeroCompte . '\' and mois like concat(\'$parent->annee\',\'%\') GROUP BY fluxid';
                    
        $listMontantFlux = new ListDynamicObject();
        $listMontantFlux->name = 'ListeMontantFlux';
        $listMontantFlux->setAssociatedRequest(null, $requeteAsso);

            
        
        
        
        
        //requ�te des op�rations r�currentes
        $requeteTotaux = "SELECT sum(montant) AS total
                        FROM operation 
                        LEFT JOIN flux ON flux.fluxId = operation.fluxId  
                        WHERE operation.nocompte='$numeroCompte' and operationRecurrente='checked'" .
                'AND date like concat(\'$parent->annee\',\'%\')';
        $listMontantTotaux = new ListDynamicObject();
        $listMontantTotaux->name = 'ListeMontantOpeRecurrente';
        $listMontantTotaux->setAssociatedRequest(null, $requeteTotaux);

        //requ�te des calculs concernant l'�pargne
        $requeteEpargne = "SELECT sum(montant) AS total
                        FROM operation 
                        LEFT JOIN flux ON flux.fluxId = operation.fluxId  
                        WHERE operation.nocompte='$numeroCompte' and entreeEpargne='checked'" .
                'AND date like concat(\'$parent->annee\',\'%\')';
        $listMontantEpargne = new ListDynamicObject();
        $listMontantEpargne->name = 'ListeMontantEpargne';
        $listMontantEpargne->setAssociatedRequest(null, $requeteEpargne);
        
        $premiereAnnee = $p_contexte->m_dataRequest->getData('premiereAnnee');
        $derniereAnnee = '';
        if ($p_contexte->m_dataRequest->getData('derniereAnnee') == null) {
            $derniereAnnee = $premiereAnnee.'-12-31';
        } else {
            $derniereAnnee = $p_contexte->m_dataRequest->getData('derniereAnnee').'-12-31';
        }
        $premiereAnnee.='-01-01';
        //$p_contexte->m_dataRequest->getData('dernierReleve');
        //requ�te principale
        $l_requete = "SELECT distinct substr(date,1,4) AS annee FROM operation WHERE date between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' order by annee";

        $listeReleves = new ListDynamicObject();
        $listeReleves->name = 'ListeAnnees';
        $listeReleves->setAssociatedKey($listMontantFlux);
        $listeReleves->setAssociatedKey($listMontantTotaux);
        $listeReleves->setAssociatedKey($listMontantEpargne);
        $listeReleves->request($l_requete);
        $p_contexte->addDataBlockRow($listeReleves);

        
        $requeteMontantFils='SELECT sum(montant) AS total, substr(date, 1, 4) as date, fluxId
                        FROM operation 
                        WHERE operation.nocompte=\''.$numeroCompte.'\' and fluxId=$parent->fluxId
                        AND date  between \''.$premiereAnnee.'\' and \''.$derniereAnnee.'\' group by substr(date, 1, 4)';
        $montantFluxFils = new ListDynamicObject();
        $montantFluxFils->name='MontantFluxFils';
        $montantFluxFils->setAssociatedRequest(null, $requeteMontantFils);
        
        
        //flux fils
        $requeteFlux = "SELECT fluxId, flux FROM flux WHERE compteId='$numeroCompte'". ' AND fluxMaitreId=$parent->fluxId ORDER BY flux';
        $listFluxFils = new ListDynamicObject();
        $listFluxFils->name = 'ListeFluxFils';
        $listFluxFils->setAssociatedKey($montantFluxFils);
        $listFluxFils->setAssociatedRequest(null, $requeteFlux);
        
        //liste des flux
        $listeFlux = new ListDynamicObject();
        $listeFlux->name = 'ListeFlux';
        $listeFlux->setAssociatedKey($listFluxFils);
        $listeFlux->request("SELECT DISTINCT flux.fluxId, flux, operationRecurrente , flux.fluxMaitre FROM stat_flux 
                        LEFT JOIN flux ON flux.fluxId = stat_flux.fluxId 
                                                WHERE concat(mois, '-15') between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' and flux.fluxid IN ($fluxSelect) ORDER BY flux");
                                                //WHERE date between '$premiereAnnee' and '$derniereAnnee' and nocompte='$numeroCompte' ORDER BY flux");
        $p_contexte->addDataBlockRow($listeFlux);
        
    }
}