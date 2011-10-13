<?
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 * Modified to fit needs of the Bricks database system fork
 * @author Isaac Jessup <ibjessup@comcast.net>
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class FormAttributeKeyCategoryItemSelectorHelper {
	
	
	public function __construct(){
		$this->view = View::getInstance();			
		$this->html = Loader::helper('html');
	}
	
	public function addHeaderItems($to=NULL){
		if(is_null($to)){
			$to = $this->view;
		}
		$to->addHeaderItem($this->html->javascript('jquery.ui.js'));
		$to->addHeaderItem($this->html->javascript('jquery.metadata.js'));
		$to->addHeaderItem($this->html->javascript('jquery.tmpl.js'));
		$to->addHeaderItem($this->html->javascript('ccm.attributekeycategory.js'));
	}
	
	public function selectItems($akCategoryHandle, $fieldName, $values=array(), $max=0, $searchInstance=NULL, $wrapAttrs=array(), $jsInit=TRUE, $itemActions=NULL) {
		
		if(is_null($searchInstance)){
			$searchInstance = $akCategoryHandle.'_search';
		}
		if(is_null($itemActions)){
			$itemActions = array(
				'quick'=>array(
					'remove'=>array(
						'label'=>t('Remove from list'),
						'icon'=>ASSETS_URL_IMAGES.'/icons/remove.png'
					)
				),
				'context'=>array(
					'properties'=>array(
						'label'=>t('Properties'),
						'icon'=>ASSETS_URL_IMAGES.'/icons/edit_small.png',
						'can'=>'write'
					),
					'remove'=>array(
						'label'=>t('Remove from list'),
						'icon'=>ASSETS_URL_IMAGES.'/icons/remove.png'
					)
				)
			);
		}
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
			
		$baseId = uniqid($searchInstance);
		$wrapId = $baseId.'_selector';
		$jsInitArgs = array();
		
		$text = Loader::helper('text');
		
		$html .= '<table width="100%" id="'.$baseId.'_table" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0"><thead>'; 
		Loader::model('attribute_key_category_item_list');
		$columns = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		$colList = $columns->getColumns();
		
		if(is_array($colList)) foreach($columns->getColumns() as $col) {
			$html .= '<th class="'.$col->getColumnKey().'">'.$col->getColumnName().'</th>';
		}
		$html .= '<th width="30" align="right" class="list-actions">';
		
		if($akcip->canSearch()){
			$html .= '<a class="search" href="javascript:;" title="'.t('Search Existing').'"><img src="' . ASSETS_URL_IMAGES . '/icons/search.png" width="16" height="16" /></a>';
		}
		if($akcip->canAdd()){
			$html .= '<a class="create" href="javascript:;" title="'.t('Create Item').'"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a>';
		}
		$html .= '</th>';
		$html .= '</tr></thead><tbody id="' . $baseId . '_body" >';
		//Create the item row template
		$jsInitArgs['itemTemplate'] = '';
		
		$imgLoading = '<img src="'.ASSETS_URL_IMAGES.'/throbber_white_16.gif" class="loading" />';
		$imgWarning = '<img src="'.ASSETS_URL_IMAGES.'/icons/warning.png" class="warning" />';
		$imgError = '<img src="'.ASSETS_URL_IMAGES.'/icons/error.png" class="error" />';
		
		$tmpl .= '<tr class="ccm-list-record" data-akciid="${id}">';
		$tmpl .= '{{if loading}}<td colspan="'.count($colList).'">'.$imgLoading.' '.t('Loading item #%s', '${id}').'</td>';
		$tmpl .= '{{else typeof error !== "undefined" && error == 404}}<td colspan="'.count($colList).'">'.$imgError.' '.t('Could not find item #%s', '${id}').'</td>';
		$tmpl .= '{{else !_can.read || typeof error !== "undefined" && error == 401}}<td colspan="'.count($colList).'">'.$imgWarning.' '.t('You are not authorized to view item #%s', '${id}').'</td>';	
		$tmpl .= '{{else}}';
		
		$colKeys = array();
		if(is_array($colList)) foreach($colList as $col){
			$colKeys []= $col->getColumnKey();
			$tmpl .= '<td class="'.$col->getColumnKey().'">${'.$col->getColumnKey().'}</td>';
		}
		$jsInitArgs['itemRefreshParams'] = array('columns[]'=>$colKeys);
		$tmpl .= '{{/if}}';
		if(is_array($itemActions)){
			$tmpl .= '<td class="item-actions">';
			//Quick actions
			if(is_array($itemActions['quick'])){
				foreach($itemActions['quick'] as $actionKey=>$action){
					if(is_string($action['can'])) $tmpl .= '{{if typeof _can === "undefined" || _can.'.$action['can'].'}}';
					$tmpl .= '<a class="'.$actionKey.'" data-action="'.$actionKey.'" href="javascript:;"><img src="'.$action['icon'].'" title="'.$action['label'].'"/></a>';
					if(is_string($action['can'])) $tmpl .= '{{/if}}';
				}
			}
			//Context menu
			if(is_array($itemActions['context'])){
				$tmpl .= '<div class="ccm-menu item-actions" style="display:none;position:absolute;"><ul>';
				foreach($itemActions['context'] as $actionKey=>$action){
					if(is_string($action['can'])) $tmpl .= '{{if typeof _can === "undefined" || _can.'.$action['can'].'}}';
					$tmpl .= '<a class="'.$actionKey.'" data-action="'.$actionKey.'" href="javascript:;">'.(is_string($action['icon']) ? '<img src="'.$action['icon'].'" alt="'.$action['label'].'"/> ' : '').$action['label'].'</a>';
					if(is_string($action['can'])) $tmpl .= '{{/if}}';
				}
				$tmpl .= '</ul></div>';
			}
			//Form field
			$tmpl .= '<input type="hidden" name="'.$fieldName.'" value="${id}" />';
			$tmpl .= '</td>';
		}
		//$tmpl .= str_replace("%input%", '<input type="hidden" name="'.$fieldName.'" value="${id}" />', $itemActionsCell);
		$tmpl .= '</tr>';
		
	
		//Add existing item rows
		//TODO: Need to look at the fieldName in the request to see if values were submitted, in case we're in a postback
		if(!empty($values)) {
			foreach($values as $akciId) {
				$akci = $akc->getItemObject($akciId);
				if($akci) {
					$html .= '<tr class="ccm-list-record" data-akciid="'.$akciId.'"><td colspan="'.count($colList).'">'.$imgLoading.' '.t('Loading item #%s', $akciId).'</td><td></td></tr>';
				}
			}
		}
		
		
		$html .= '</tbody></table>';
		
		$html .= '<small class="count">';
		if($max > 0 && !is_infinite($max)){
			$html .= t('%s of %s maximum', '<var>'.count($values).'</var>', "<var>$max</var>");
		}else{
			$html .= t('%s item(s)', '<var>'.count($values).'</var>');
		}
		$html .= '</small>';
		
		
		//Setup the js init args
		$json = Loader::helper('json');
		$jsInitArgs['max'] = $max;
		$jsInitArgs['baseId'] = $baseId;
		$jsInitArgs['akcHandle'] = $akCategoryHandle;
		$jsInitArgs['fieldName'] = $fieldName;
		$jsInitArgs['itemTemplate'] = $tmpl;
		$jsInitArgs['itemSearchDialog'] = array('title'=>t('Choose %s Items', $text->unhandle($akCategoryHandle)));
		$jsInitArgs['itemCreateDialog'] = array('title'=>t('Create %s Item', $text->unhandle($akCategoryHandle)));
		$jsInitArgs['itemSearchParams'] = array('searchInstance'=>$searchInstance);
		
		if($jsInit){			
			$html .= '
			<script type="text/javascript">
			
				$(function(){
					$("#'.$wrapId.'").ccm_akcItemSelector();
				});
				
			</script>';		
		}		
		
		//Add the wrapper
		$wrapAttrDefaults = $finalAttrs = array(
			'id'=>$wrapId,
			'class'=>'ccm-akc-item-selector',
			'data-options-ccm_akcitemselector'=>$json->encode($jsInitArgs)
		);
		if(is_array($wrapAttrs)){
			$finalAttrs = array_merge($wrapAttrDefaults, $wrapAttrs);
		}
		
		foreach($finalAttrs as $attr=>$val){
			if(($attr == 'class') && strpos($val, $wrapAttrDefaults[$attr])===FALSE){
				$val .= $wrapAttrDefArr[$attr];
			}
			$wrapAttrStr .= $attr.'="'.htmlspecialchars($val, ENT_QUOTES, NULL, FALSE).'" ';
		}
		
		$html = "<div $wrapAttrStr>$html</div>";
		
		return $html;
	}
	
	
}