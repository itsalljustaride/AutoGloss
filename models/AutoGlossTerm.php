<?php
/**
 * Auto Gloss term.
 */

/**
 * An auto_gloss_terms row.
 * 
 * @package AutoGloss
 */
class AutoGlossTerm extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{

	public $created_by_user_id;
	public $replace_set;
	public $ignore_set = '';
	public $custom_regex = '';
	public $description = '';
	public $added;
	public $modified;
	
	protected function _initializeMixins()
  {
      $this->_mixins[] = new Mixin_Search($this);
  }

  public function getRecordUrl($action = 'show')
  {
      return array('module' => 'auto-gloss', 'controller' => 'index', 
                   'action' => $action, 'id' => $this->id);
  }

  public function getResourceId()
  {
			return 'AutoGloss_Term';
  }

}