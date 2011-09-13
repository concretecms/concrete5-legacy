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
	
	public function selectItems($akCategoryHandle, $fieldName, $values, $akID, $searchInstance = false) {
		$baseID = uniqid("ccmAttributeKeyCategoryItemSelect$akID_");
		$itemActionsCell = '<td class="item-actions"><a class="remove" href="javascript:;"><img width="16" height="16" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a>%input%</td>';
		$fieldName = $fieldName.'[]';
		$html = '<div id="'.$baseID.'"><input type="hidden" name="'.$fieldName.'" value="" />';
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
					$html .= '<tr class="" id="ccmAttributeKeyCategoryItemSelect'.$akID.'_'.$akci->ID.'">';
					if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
						$html .= '<td>'.$col->getColumnValue($akci).'</td>';
					}
					$html .= str_replace("%input%", "<input type=\"hidden\" name=\"$fieldName\" value=\"$akci->ID\" />", $itemActionsCell);
				}
			}
		} else {
			$html .= '<tr class="ccm-attribute-key-category-selected-item-none"><td colspan="3">' . t('No items selected.') . '</td></tr>';
		}
		$html .= '</tbody></table></div>';

		
		$html .= '
		<script type="text/javascript">
		
			$(function(){
				$("#'.$baseID.'").ccm_attributeKeyCategoryItemSelector({
					fieldName:"'.$fieldName.'",
					selectItemDialog:{
						title:"'. t('Choose Items') .'"
					},
					selectItemParameters:{
						akCategoryHandle:"'.$akCategoryHandle.'",
						akID:"'.$akID.'",
						searchInstance:"'.$baseID.'"
					},
					itemActionsCell:"'.addslashes(str_replace('%input%', '', $itemActionsCell)).'"
				});
			});
			
		</script>';	
		return $html;
	}
	
	
}