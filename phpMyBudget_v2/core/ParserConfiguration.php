<?php

class ParserConfiguration {

    public static function getAction($p_domaine, $p_service) {
        return self::parseConf($p_domaine, $p_service);
    }

    private static function parseConf($p_domaine, $p_service) {
		//lecture du fichier de conf
        $xml = simplexml_load_file('./application/application.xml');
		//recherche du domaine/service
		$result = $xml->xpath('/configuration/domaines/domaine[@name="'.$p_domaine.'"]/service[@name="'.$p_service.'"]');
		$page=null;
		if($result != null){
			$page = new PageDescription($p_domaine, $p_service, (string) $result[0]['classe'], (string) $result[0]['methode'], (string) $result[0]['isPrivee']);
		} else {
			die("Domaine/service $p_domaine/$p_service introuvable!!!");
		}
		
		
		if (isset($result[0]['render'])) {
			$page->setRender((string) $result[0]['render']);
		}
		
		$page->setXslFile($result[0]['xsl']);
		$page->setPrivee($result[0]['isPrivee']);
		
		if (isset($result[0]['paramFlow'])) {
			$page->paramFlow((string) $result[0]['paramFlow']);
		}
		/*foreach ($actions->children() as $parse) {

            if ((string) $parse['name'] == $p_action) {
                $page = new PageDescription((string) $parse['name'], (string) $parse['isPrivee']);

                if (isset($parse['create'])) {
                    Logger::getInstance()->addLogMessage('Controller create:' . $parse['create']);
                    $page->setCreateService((string) $parse['create']);
                }

                if (isset($parse['update'])) {
                    $page->setUpdateService((string) $parse['update']);
                    Logger::getInstance()->addLogMessage('Controller update:' . $parse['update']);
                }

                if (isset($parse['delete'])) {
                    $page->setDeleteService((string) $parse['delete']);
                    Logger::getInstance()->addLogMessage('Controller delete:' . $parse['delete']);
                }

                if (isset($parse['display'])) {
                    $page->setDisplayService((string) $parse['display']);
                    Logger::getInstance()->addLogMessage('Controller display:' . $parse['display']);
                } else {
                    die('une page doit comporter au moins une action display!!!');
                }
                
                if (isset($parse['render'])) {
                    $page->setRender((string) $parse['render']);
                }

                if (isset($parse['paramFlow'])) {
                    $page->paramFlow((string) $parse['paramFlow']);
                    Logger::getInstance()->addLogMessage('Flux:' . $parse['paramFlow']);
                }

                $page->setXslFile($parse['xsl']);
                $page->setPrivee($parse['isPrivee']);
            }
        }*/
        return $page;
    }

}

?>
