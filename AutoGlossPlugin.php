<?php
/**
 * @package AutoGloss
 * @copyright Copyright 2013, Johnathon Beals
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPLv3 or any later version
 */

include 'RecursiveDOMIterator.php';

class AutoGlossPlugin extends Omeka_Plugin_AbstractPlugin
{
  protected $_hooks = array('initialize', 'admin_head', 'public_head',
       							'config', 'config_form', 'install', 'uninstall', 
									'public_footer', 'upgrade', 'define_acl');
  
  protected $_filters = array('admin_navigation_main', 'display_elements');
  
  public function hookInstall()
  {
      $db = get_db();
      $sql = "
      CREATE TABLE `{$db->AutoGlossTerm}` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `created_by_user_id` int(10) unsigned NOT NULL,
          `replace_set` text COLLATE utf8_unicode_ci NOT NULL,
          `ignore_set` text COLLATE utf8_unicode_ci NOT NULL,
					`custom_regex` text COLLATE utf8_unicode_ci NOT NULL,
					`description` text COLLATE utf8_unicode_ci NOT NULL,
					`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
          PRIMARY KEY (`id`)
      ) ENGINE=INNODB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $db->query($sql);

			set_option('auto_gloss_settings_first_only', true);
			
	        $options = array();
	        $fields = array('Description');

	        foreach ($fields as $field) {
	            $key = 'gloss_'.strtolower($field);
	            $element = $this->_db->getTable('Element')->findByElementSetNameAndElementName("Dublin Core", "$field");
	            $options[$key] = $element->id;
	        }

	        $options = serialize($options);
			set_option('auto_gloss_settings_gloss_fields', $options);
  }
  
  public function hookUninstall()
  {
      $db = get_db();
      $sql = "DROP TABLE IF EXISTS `{$db->AutoGlossTerm}`;";
      $db->query($sql);

      delete_option('auto_gloss_settings_first_only');
      delete_option('auto_gloss_settings_gloss_fields');
  }

  public function hookUpgrade($args)
  {
  }

  public function hookInitialize()
  {
	  function glossText($text, $args)
	  {		  						
		$terms = get_db()->getTable('AutoGlossTerm')->findAll();
		foreach($terms as $term_set){
			// Create an array of the Replace terms
			$term_versions = $term_set->replace_set;
			$term_versions = preg_split('/\s*,\s*/', $term_versions);
			uasort($term_versions, function($a, $b) {
			    return strlen($b) - strlen($a);
			});
		
			//Create an array of the Ignore terms
			$term_ignore_versions = $term_set->ignore_set;
			$term_ignore_versions = preg_split('/\s*,\s*/', $term_ignore_versions);
			uasort($term_ignore_versions, function($a, $b) {
			    return strlen($b) - strlen($a);
			});
		
			//Set up the search and replace values for the preg_replace function
			if ($term_set->custom_regex > ''){
				$search = $term_set->custom_regex;
				if (@preg_match($search, $text) === false){
					break;
				}
			} else {
				if ($term_set->ignore_set > ''){
					$search = '/\\b(?!'. implode('|',$term_ignore_versions) .')(' . implode('|',$term_versions) . ')(?<!'. implode('|',$term_ignore_versions) .')\\b/i';
				} else {
					$search = '/(?!<span.*>)\\b(' . implode('|',$term_versions) . ')\\b(?!<\\/span>)/i';
				}
			}
			$replace = '<span class="tooltip" title="<span class=\'tooltip_popup\'>'.str_replace('"',"'",$term_set->description).'</span>">$0</span>';
		
			$dom = new DOMDocument;				// create new DOMDocument instance
			libxml_use_internal_errors(true);
			$text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');
			$dom->loadHTML($text);      // load DOMDocument with XML data

			$dit = new RecursiveIteratorIterator(
			            new RecursiveDOMIterator($dom),
			            RecursiveIteratorIterator::SELF_FIRST);

			foreach($dit as $node) {
				if(	$node->nodeType === XML_TEXT_NODE && $node->parentNode->getAttribute('class') !== "tooltip" ) {
					if (get_option('auto_gloss_settings_first_only') == 1) {
						preg_match($search, $node->nodeValue, $match, PREG_OFFSET_CAPTURE);
						if ( $match ) {
							// Make sure offset is character offset, not byte offset
							$substr = substr($node->nodeValue, 0, $match[0][1]);
							$encoding = 'UTF-8';
							$offset = mb_strlen($substr, $encoding ?: mb_internal_encoding());
						
							// Split the text node and return the new node minus the term text
							$termTextNode = $node->splitText($offset);
							$newTextNode = $termTextNode->splitText(mb_strlen($match[0][0]));
						
							// Create and append the glossed term node in place of the term text node
							$tooltipSpan = $dom->createElement('span',$match[0][0]);
							$tooltipSpan->setAttribute('class','tooltip');
							$tooltipSpan->setAttribute('title',$term_set->description);
							$node->parentNode->replaceChild($tooltipSpan, $termTextNode);
						
							break;
						}
					} else {
						preg_match_all($search, $node->nodeValue, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
						if ( $matches ) {
							foreach($matches as $match) {
								// Make sure offset is character offset, not byte offset
								$substr = substr($node->nodeValue, 0, $match[0][1]);
								$encoding = 'UTF-8';
								$offset = mb_strlen($substr, $encoding ?: mb_internal_encoding());
						
								// Split the text node and return the new node minus the term text
								$termTextNode = $node->splitText($offset);
								$newTextNode = $termTextNode->splitText(mb_strlen($match[0][0]));
						
								// Create and append the glossed term node in place of the term text node
								$tooltipSpan = $dom->createElement('span',$match[0][0]);
								$tooltipSpan->setAttribute('class','tooltip');
								$tooltipSpan->setAttribute('title',$term_set->description);
								$node->parentNode->replaceChild($tooltipSpan, $termTextNode);
						
								break;
							}
						}
					}
				}
			}
		
			$text_temp = $dom->saveHTML();
			$text = $text_temp;
		}
			
	   return $text;
	  }
	  
	  $elementsToGloss = array($this->_db->getTable('Element')->find(unserialize(get_option('auto_gloss_settings_gloss_fields'))));
	  foreach( $elementsToGloss as $element ) {
		  $elementSetName = $this->_db->getTable('ElementSet')->find($element->element_set_id)->name;
		  add_filter(array('Display', 'Item', $elementSetName, $element->name),'glossText');
	  }
  }
	
	public function hookConfigForm()
  {
      include 'config-form.php';
  }

  public function hookConfig()
  {
      set_option('auto_gloss_settings_first_only', (int)(boolean)$_POST['auto_gloss_settings_first_only']);	  
	  set_option('auto_gloss_settings_gloss_fields', serialize($_POST['auto_gloss_settings_gloss_fields']));
  }

  public function hookAdminHead()
  {
      $this->_head();
  }

  public function hookPublicHead()
  {
      $this->_head();
  }
  
  public function hookPublicFooter(){
  	$tooltipsterJSInit = "<script>
					        $(document).ready(function() {
					            $('.tooltip').tooltipster({
								    animation: 'fade',
								    delay: 200,
									interactive: true,
								    theme: '.tooltipster-default',
								    touchDevices: true,
								    trigger: 'hover'
								});
					        });
					    </script>";
	  
	// Add the Javascript Tooltip init
	echo $tooltipsterJSInit;
  }
   
  private function _head()
  {
	queue_js_url('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
	queue_js_file('jquery.tooltipster.min');
	queue_css_file('tooltipster','all','','css/tooltipster');
	queue_css_file('agstyle');
  }  

  public function hookDefineAcl($args)
  {
      // Restrict access to super and admin users.
      $args['acl']->addResource('AutoGloss_Index');
  }

  public function filterAdminNavigationMain($nav)
  {
      if(is_allowed('AutoGloss_Index', 'index')) {
          $nav[] = array('label' => __('Auto Gloss'), 'uri' => url('auto-gloss'));
      }
      return $nav;
  }
  
  public function filterDisplayElements($elementsBySet) {
	  $elementsToGloss = $this->_db->getTable('Element')->find(unserialize(get_option('auto_gloss_settings_gloss_fields')));
	  //echo $element->name;
	  foreach($elementsBySet as $set => $elements){
		  foreach($elements as $key => $element){
			  if ($element->name == $elementsToGloss->name){
				  //echo print_r($element);
			  }
		  }
	  }
      return $elementsBySet;
  }
  
}
