<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SegmentRequest
 *
 * @author ingeni
 */
class SegmentRequest {
    //put your code here
    public function display(ContextExecution $p_contexte){
        $reponse = new ReponseAjax();
        $reponse->valeur=1;
        $p_contexte->m_dataRequest->addDataBlockRow($reponse);
    }
    
}

?>
