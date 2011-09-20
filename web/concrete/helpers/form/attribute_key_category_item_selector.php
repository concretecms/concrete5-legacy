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
	
	public function selectItems($akCategoryHandle, $fieldName, $values=array(), $wrapAttrs=array(), $searchInstance=NULL, $jsInit=TRUE, $jsInitArgs=array()) {
		if(is_null($searchInstance)){
			$searchInstance = uniqid($akCategoryHandle.'_selector');
		}
		$baseID = isset($wrapAttrs['id']) ? $wrapAttrs['id'] : $searchInstance;
		$itemActionsCell = '<td class="item-actions"><a class="remove" href="javascript:;"><img width="16" height="16" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a>%input%</td>';
		$fieldName = $fieldName.'[]';
		
		$html .= '<table width="100%" id="'.$baseID.'_table" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0"><thead>'; 
		Loader::model('attribute_key_category_item_list');
		$columns = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
			$html .= '<th>'.$col->getColumnName().'</th>';
		}
		$html .= '<th width="30px" align="right"><a class="ccm-attribute-key-category-select-item" href="javascript:;"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a></th>';
		$html .= '</tr></thead><tbody id="' . $baseID . '_body" >';
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
			$html .= '<tr class="ccm-attribute-key-category-selected-item-none"><td colspan="3">' . t('No items selected.') . '</td></tr>';
		}
		$html .= '</tbody></table>';

		if($jsInit){
			
			//Setup the js init args
			$jsInitArgs['fieldName'] = $fieldName;
			$jsInitArgs['selectItemDialog'] = array('title'=>t('Choose Items'));
			$jsInitArgs['selectItemParameters'] = array('akCategoryHandle'=>$akCategoryHandle);
			$jsInitArgs['itemActionsCell'] = str_replace('%input%', '', $itemActionsCell);
			
			$json = Loader::helper('json');
			$jsInitArgsStr = $json->encode($jsInitArgs);
			
			$html .= '
			<script type="text/javascript">
			
				$(function(){
					$("#'.$baseID.'").ccm_attributeKeyCategoryItemSelector('.$jsInitArgsStr.');
				});
				
			</script>';	
		
		}
		
		//Add the wrapper
		$wrapAttrDefaults = $finalAttrs = array(
			'id'=>$baseID,
			'class'=>'ccm-attribute-key-category-item-selector'
		);
		if(is_array($wrapAttrs)){
			$finalAttrs = array_merge($wrapAttrDefaults, $wrapAttrs);
		}
		
		foreach($finalAttrs as $attr=>$val){
			if(($attr == 'class') && strpos($val, $wrapAttrDefaults[$attr])===FALSE){
				$val .= $wrapAttrDefArr[$attr];
			}
			$wrapAttrStr .= "$attr=\"$val\" ";
		}
		
		$html = "<div $wrapAttrStr>$html</div>";
		
		return $html;
	}
	
	
}