<?php

/**
 * Description of ListDynamicObject
 *
 * @author ingeni
 */
class ListDynamicObject extends ListStructure implements IList{
    
    
    public $name='';

    public $tabResult=null;
    
    public $nbLineTotal;
    public $nbLine;
    
    public $totalPage;
    public $page;
    
//    public function getData() {
//        return $this->tabData;
//    }
    
     /**
     * fonction de requ�tage
     * @param unknown_type $st1 requ�te
     * @param unknown_type $st2 numero de page
     * @param unknown_type $st3 inutilis�e
     */
    public function request($p_requete, $p_numPage=null, $dummy=null){
        Logger::getInstance()->addLogMessage('requete dynamique:'.$p_requete);
        
        if($p_numPage!=null){
			$stmt = self::$_pdo->query($p_requete);
			
        	if($stmt==FALSE) {
            	throw new TechnicalException(self::$_pdo->errorCode(),self::$_pdo->errorInfo() );
       		}
        	$l_tab = $stmt->fetch(PDO::FETCH_ASSOC);
        	$this->nbLineTotal = $stmt->rowCount();
        	
        	
        	$p_requete .= " LIMIT " . ($p_numPage-1)*LIGNE_PAR_PAGE . ', ' . LIGNE_PAR_PAGE;        	
        }
        
        Logger::getInstance()->addLogMessage('requete dynamique:'.$p_requete);
        $stmt = self::$_pdo->query($p_requete);
		if($stmt==FALSE) {
			Logger::getInstance()->addLogMessage('ERREUR ERREUR ');
			throw new TechnicalException(self::$_pdo->errorCode(),self::$_pdo->errorInfo() );
		}
        $this->nbLine = $stmt->rowCount();
        $stmt->setFetchMode(PDO::FETCH_INTO, $this);
        $this->tabResult = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $this->totalPage = ceil($this->nbLineTotal / LIGNE_PAR_PAGE);
        $this->page=($p_numPage==null)? 1 : $p_numPage;
        
        //ex�cute les requ�tes associ�es
        $this->callAssoc();
    }
    
    public function getName() {
        return $this->name;
    }
    
    /**
     * (non-PHPdoc)
     * @see IList::getData()
     */
    public function getData(){
        return $this->tabResult;
    }
    
    public function getNbLineTotal(){
        return $this->nbLineTotal;
    }
    
    public function getNbLine(){
        return count($this->tabResult);
    }
}
?>