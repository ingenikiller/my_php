<?php
/**
 * Description of ListObject
 * Classe permettant de r�cup�rer une liste d'objets persistents
 *
 * @author ingeni
 */
class ListObject extends ListStructure implements IList{
    
    public $name='';

    public $tabResult=null;
    
    public $nbLineTotal;
    public $nbLine;
    
    public $totalPage;
    public $page;
    
    public function requestNoPage($classe, $clause=null) {
        $l_suff = " FROM $classe";
        if($clause!=null){
            $l_suff.=" WHERE $clause";
        }
        Logger::$instance->addLogMessage('search: '. $l_suff);
        
        //requete principale
        $l_requete = 'select * '.$l_suff;
        Logger::$instance->addLogMessage('search complete: '. $l_requete);
        $stmt = self::$_pdo->query($l_requete);
        
        $this->nbLine = $stmt->rowCount();
        $this->tabResult = $stmt->fetchAll(PDO::FETCH_CLASS, $classe);//, array(self::$_pdo, $table[1]));   
        
        //appel des requetes des objets associ�s
        $this->callAssoc();
    }
    
    /**
     *fonction lan�ant une requete SQL
     * @param string $classe nom de la classe
     * @param string $clause clause SQL de recherche
     * @param int $page num�ro de page
     */
    public function request($classe, $clause=null, $page=0){
        
    	if($page==0){
    		return $this->requestNoPage($classe, $clause);
    	}
    	
    	$l_suff = " FROM $classe";
        if($clause!=null){
            $l_suff.=" WHERE $clause";
        }
        Logger::$instance->addLogMessage('search: '. $l_suff);
        
        if($page==''){
            $page=1;
        }
        
        //requete de comptage
        $l_requete = "select count(*) as total $l_suff";
        $stmt = self::$_pdo->query($l_requete);
        if($stmt==FALSE) {
            throw new TechnicalException(self::$_pdo->errorCode(),self::$_pdo->errorInfo() );
        }
        $l_tab = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->nbLineTotal = $l_tab['total'];
        //requete principale
        $l_requete = "select * $l_suff LIMIT " . ($page-1)*LIGNE_PAR_PAGE . ', ' . LIGNE_PAR_PAGE;
        Logger::$instance->addLogMessage('search complete: '. $l_requete);
        $stmt = self::$_pdo->query($l_requete);
        
        $this->nbLine = $stmt->rowCount();
        $this->tabResult = $stmt->fetchAll(PDO::FETCH_CLASS, $classe);//, array(self::$_pdo, $table[1]));   
        
        $this->totalPage = ceil($this->getNbLineTotal() / LIGNE_PAR_PAGE);
        $this->page=$page;
        //appel des requetes des objets associ�s
        $this->callAssoc();
        
         //return $this;
    }

    public function getNbRows() {
        return count($this->tabResult);
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getData(){
        return $this->tabResult;
    }
    
    public function getNbLineTotal(){
        return $this->nbLineTotal;
    }
    
    public function getNbLine(){
        return $this->nbLine;
    }
}
?>
