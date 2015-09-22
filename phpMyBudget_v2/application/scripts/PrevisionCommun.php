<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrevisionCommun
 *
 * @author ingeni
 */
class PrevisionCommun {
    //put your code here
    
    public static function genereLignes(Prevision $entete, $identete, $periodicite, $decalage, $montant){
        //$periode = $entete->annee * 100 + $decalage;
        $mois=$decalage;
        while( $mois <= 12) {
            $periode = $entete->annee * 100 + $mois;
            
            $prevision = new Prevision();
            $prevision->identete = $identete;
            $prevision->fluxId=$entete->fluxId;
            $prevision->typenr='L';
            $prevision->mois=$periode;
            $prevision->montant=$montant;
            $prevision->create();
            
            //decalage
            $mois+=$periodicite;
        }
        
        
        
    }
    
}

?>
