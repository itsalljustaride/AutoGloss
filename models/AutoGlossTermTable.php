<?php
/**
 * Simple Pages
 *
 * @copyright Copyright 2008-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Simple Pages page table class.
 *
 * @package AutoGloss
 */
class AutoGlossTermTable extends Omeka_Db_Table
{    
	  
    public function applySearchFilters($select, $params)
    {
        $alias = $this->getTableAlias();
        $paramNames = array('created_by_user_id',
                            'replace_set',
														'ignore_set',
                            'description');
                            
        foreach($paramNames as $paramName) {
            if (isset($params[$paramName])) {             
                $select->where($alias . '.' . $paramName . ' = ?', array($params[$paramName]));
            }            
        }

        if (isset($params['sort'])) {
            switch($params['sort']) {
                case 'alpha':
                    $select->order("{$alias}.title ASC");
                    $select->order("{$alias}.order ASC");
                    break;
                case 'order':
                    $select->order("{$alias}.order ASC");
                    $select->order("{$alias}.title ASC");
                    break;
            }
        }         
    }
        
    protected function _createIdToPageLookup() 
    {
        // get all of the terms
        $allTerms = $this->findAll();
        
        // create the term lookup                
        $idToTermLookup = array();
        foreach($allTerms as $term) {
            $idToTermLookup[$term->id] = $term;
        }
        
        return $idToTermLookup;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        
        
        return $select;
	
    }
}
