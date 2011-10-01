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
		$to->addHeaderItem($this->html->javascript('ccm.attributekeycategory.js'));
	}
	
	public function selectItems($akCategoryHandle, $fieldName, $values=array(), $max=0, $searchInstance=NULL, $wrapAttrs=array(), $jsInit=TRUE, $jsInitArgs=array()) {
		if(is_null($searchInstance)){
			$searchInstance = $akCategoryHandle.'_selector';
		}
		$baseId = isset($wrapAttrs['id']) ? $wrapAttrs['id'] : uniqid($searchInstance);
		$itemActionsCell = '<td class="item-actions"><a class="remove" href="javascript:;"><img width="16" height="16" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a>%input%</td>';
		
		$html .= '<table width="100%" id="'.$baseId.'_table" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0"><thead>'; 
		Loader::model('attribute_key_category_item_list');
		$columns = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
			$html .= '<th>'.$col->getColumnName().'</th>';
		}
		$html .= '<th width="30px" align="right"><a class="ccm-akc-select-item" href="javascript:;" title="'.t('Add Item(s)').($max > 0 && !is_infinite($max) ? ' : '.t('%s maximum', $max) : '').'"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a></th>';
		$html .= '</tr></thead><tbody id="' . $baseId . '_body" >';
		if(!empty($values)) {
			foreach($values as $akci) {
				if($akci) {
					$html .= '<tr class="ccm-list-record" id="ccmAttributeKeyCategoryItemSelect'.$akID.'_'.$akci->ID.'">';
					if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
						$html .= '<td>'.$col->getColumnValue($akci).'</td>';
					}
					$html .= str_replace("%input%", "<input type=\"hidden\" name=\"$fieldName\" value=\"$akci->ID\" />", $itemActionsCell);
				}
			}
		} else {
			$html .= '<tr class="ccm-akc-selected-item-none"><td colspan="3">' . t('No items selected.') . '</td></tr>';
		}
		$html .= '</tbody></table>';
		
		
		if($jsInit){
			
			//Setup the js init args
			$jsInitArgs['max'] = $max;
			$jsInitArgs['baseId'] = $baseId;
			$jsInitArgs['akcHandle'] = $akCategoryHandle;
			$jsInitArgs['fieldName'] = $fieldName;
			$jsInitArgs['selectItemDialog'] = array('title'=>t('Choose Items'));
			$jsInitArgs['itemActionsCell'] = str_replace('%input%', '', $itemActionsCell);
			$jsInitArgs['itemSearchParams'] = array('searchInstance'=>$searchInstance);
			$json = Loader::helper('json');
			$jsInitArgsStr = $json->encode($jsInitArgs);
			
			$html .= '
			<script type="text/javascript">
			
				$(function(){
					$("#'.$baseId.'").ccm_akcItemSelector('.$jsInitArgsStr.');
				});
				
			</script>';	
		
		}
		
		//Add the wrapper
		$wrapAttrDefaults = $finalAttrs = array(
			'id'=>$baseId,
			'class'=>'ccm-akc-item-selector'
		);
		if(is_array($wrapAttrs)){
			$finalAttrs = array_merge($wrapAttrDefaults, $wrapAttrs);
		}
		
		foreach($finalAttrs as $attr=>$val){
			if(($attr == 'class') && strpos($val, $wrapAttrDefaults[$attr])===FALSE){
				$val .= $wrapAttrDefArr[$attr];
			}
			$wrapAttrStr .= $attr.'="'.addslashes($val).'" ';
		}
		
		$html = "<div $wrapAttrStr>$html</div>";
		
		return $html;
	}
	
	
}