<?php

class GestionSegmentService extends ServiceStub {
	
	public function getSegment(ContextExecution $p_contexte) {
        $cleseg=$p_contexte->m_dataRequest->getData('cleseg');
        if($cleseg!=null){
            $liste = new ListObject();
            $liste->name ='Segments';
            $liste->request('Segment', "cleseg='$cleseg' order by numord");
            $p_contexte->addDataBlockRow($liste);
        }else {
            $liste = new ListObject();
            $liste->name ='Segments';
            $liste->request('Segment', "cleseg='CONF' order by codseg");
            $p_contexte->addDataBlockRow($liste);
        }
    }
	
}

?>