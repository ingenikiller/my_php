<?php

class CreateService {
    //put your code here
    public function display(ContextExecution $p_contexte) {
        $page=$p_contexte->m_dataRequest->getData('page');
        switch ($page) {
            case 'FLUX_C':
                $userid = $p_contexte->getUser()->userId;
                
                $listeCompte = new ListObject();
                $listeCompte->name='ListeComptes';
                $listeCompte->request('Comptes', "userId=$userid");
                $p_contexte->addDataBlockRow($listeCompte);
                break;

            default:
                break;
        }
    }
    
}
?>