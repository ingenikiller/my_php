<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SegmentsService
 *
 * @author ingeni
 */
class SegmentsService {
    
    public function display(ContextExecution $p_contexte) {
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
    
    public function update(ContextExecution $p_contexte) {
        $indice=1;
        Logger::getInstance()->addLogMessage("indice $indice");
        Logger::getInstance()->addLogMessage("cleseg-$indice:".$p_contexte->m_dataRequest->getData('cleseg-'.$indice));
        
        while($p_contexte->m_dataRequest->getData('cleseg-'.$indice)!=null) {
            
            $codseg = '';
            $segment= new Segment();
            $segment->codseg=$p_contexte->m_dataRequest->getData('codseg-'.$indice);
            $segment->cleseg=$p_contexte->m_dataRequest->getData('cleseg-'.$indice);
            $segment->load();
            $segment->fieldObject($p_contexte->m_dataRequest,'', '-', $indice);
            $segment->save();
            $indice++;
        }
        
        
    }

    /**
     *
     * @param ContextExecution $p_contexte 
     */
    public function create(ContextExecution $p_contexte) {
        $segment = new Segment();
        $segment->cleseg=$p_contexte->m_dataRequest->getData('Ncleseg');
        $segment->codseg=$p_contexte->m_dataRequest->getData('Ncodseg');
        $segment->fieldObject($p_contexte->m_dataRequest,'N');
        //$segment->libCourt=$p_contexte->m_dataRequest->getData('Nlibcourt');
        //$segment->libLong=$p_contexte->m_dataRequest->getData('Nliblong');
        $segment->numord=$p_contexte->m_dataRequest->getData('Nnumord');
        $segment->save();
//        $liste = new ListObject();
//        $liste->name ='Segments';
//        $liste->request('Segment', "cleseg='".$p_contexte->m_dataRequest->getData('NCleseg')."'");
//        $p_contexte->addDataBlockRow($liste);
    }
    
    public function delete(ContextExecution $p_contexte){
        
        $cleseg=$p_contexte->m_dataRequest->getData('cleseg');
        $codseg=$p_contexte->m_dataRequest->getData('codseg');
        if($codseg!=null){
            $segment = new Segment();
            $segment->cleseg=$cleseg;
            $segment->codseg=$codseg;
            $segment->delete();

            if($cleseg=='CONF'){
                $requete = "DELETE   FROM segment WHERE cleseg='$codseg'";
                $pdo = ConnexionPDO::getInstance();
                $stmt = $pdo->query($requete);
            }
            
        }
//        $cleseg=$p_contexte->m_dataRequest->getData('dcodseg');
//        if($cleseg!=null){
//            
//        }
    }
    
    
}
?>