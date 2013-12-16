<?php
/**
 * The Auto Gloss controller.
 * 
 * @package AutoGloss
 */
class AutoGloss_IndexController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
				$this->_helper->db->setDefaultModelName('AutoGlossTerm');
    }
    
    public function indexAction()
    {
				$this->view->terms = $this->_helper->db->getTable('AutoGlossTerm')->findAll();
    }

    public function showAction()
    {
        // Get the page object from the passed ID.
        $termId = $this->_getParam('id');
        $term = $this->_helper->db->getTable('AutoGlossTerm')->find($termId);
                
        // Set the page object to the view.
        $this->view->term = $term;
    }

    public function addAction()
    {
        // Create a new term.
        $term = new AutoGlossTerm;
        
        // Set the created by user ID.
        $term->created_by_user_id = current_user()->id;

        $term->replace_set = '';
        $term->ignore_set = '';
				$term->description = '';
				
        $this->view->form = $this->_getForm($term);
        $this->_processPageForm($term, 'add');
    }
    
    public function editAction()
    {
        // Get the requested page.
        $term = $this->_helper->db->findById();
        $this->view->form = $this->_getForm($term);
        $this->_processPageForm($term, 'edit');
    }
    
    protected function _getForm($term = null)
    { 
        $formOptions = array('type' => 'auto_gloss_term', 'hasPublicPage' => true);
        if ($term) {
            $formOptions['record'] = $term;
        }
        
        $form = new Omeka_Form_Admin($formOptions);
        $form->addElementToEditGroup(
            'text', 'replace_set',
            array(
                'id' => 'auto-gloss-replace-set',
                'value' => $term->replace_set,
                'label' => __('Term Variations'),
                'description' => __('The term, and any variations, separated by commas (required)'),
                'required' => true
            )
        );
        
        $form->addElementToEditGroup(
            'text', 'ignore_set',
            array(
                'id' => 'auto-gloss-ignore-set',
                'value' => $term->ignore_set,
                'label' => __('Ignore Terms'),
                'description' => __(
                    'Ignore these variations, separated by commas'
                )
            )
        );

        $form->addElementToEditGroup(
            'text', 'custom_regex',
            array('id' => 'auto-gloss-custom-regex',
                'value' => $term->custom_regex,
                'label' => __('Custom Regular Expression'),
                'description' => __(
                    'A custom regular expression (must be valid regex) in case you need to override the default substitution logic.'
                ),
                'required' => false
            )
        );
                 
        $form->addElementToEditGroup(
            'textarea', 'description',
            array('id' => 'auto-gloss-description',
                'cols'  => 50,
                'rows'  => 25,
                'value' => $term->description,
                'label' => __('Description'),
                'description' => __(
                    'Add content for tooltip, including HTML markup  (required)'
                ),
                'required' => true
            )
        );
                
        return $form;
    }
    
    /**
     * Process the page edit and edit forms.
     */
    private function _processPageForm($term, $action)
    {
        if ($this->getRequest()->isPost()) {
            // Attempt to save the form if there is a valid POST. If the form 
            // is successfully saved, set the flash message, unset the POST, 
            // and redirect to the browse action.
            try {
                $term->setPostData($_POST);
                if ($term->save()) {
                    if ('add' == $action) {
                        $this->_helper->flashMessenger(__('The term "%s" has been added.', $term->id), 'success');
                    } else if ('edit' == $action) {
                        $this->_helper->flashMessenger(__('The term "%s" has been edited.', $term->id), 'success');
                    }
                    
                    $this->_helper->redirector('index');
                    return;
                }
            // Catch validation errors.
            } catch (Omeka_Validate_Exception $e) {
                $this->_helper->flashMessenger($e);
            }
        }

        // Set the term object to the view.
        $this->view->term = $term;
    }

		protected function _redirectAfterDelete($record) {
			// Always go to index.
      $this->_helper->redirector('index');
      return;
		}

    protected function _getDeleteSuccessMessage($record)
    {
        return __('The term "%s" has been deleted.', $record->id);
    }

}