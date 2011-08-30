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
		$html = '<input type="hidden" name="'.$fieldName.'[]" value="" />';
		$html .= '<table width="100%" id="ccmAttributeKeyCategoryItemSelect' . $akID . '" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">'; 
		Loader::model('attribute_key_category_item_list');
		$columns = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
			$html .= '<th>'.$col->getColumnName().'</th>';
		}
		$html .= '<th width="30px" align="right"><a class="ccm-attribute-key-category-select-item" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose Items') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/bricks/search_dialog?mode=choose_multiple&akCategoryHandle='.$akCategoryHandle.'&akID='.$akID.'&searchInstance=akID_'.$akID*time().'&fieldName='.$fieldName.'[]"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a></th>';
		$html .= '</tr><tbody id="ccmAttributeKeyCategoryItemSelect' . $akID . '_body" >';
		if(!empty($values)) {
			foreach($values as $akci) {
				if($akci) {
					$html .= '<tr class="" id="ccmAttributeKeyCategoryItemSelect'.$akID.'_'.$akci->ID.'">';
					if(is_array($columns->getColumns())) foreach($columns->getColumns() as $col) {
						$html .= '<td>'.$col->getColumnValue($akci).'</td>';
					}
					$html .= '<td align="center"><input type="hidden" value="'.$akci->ID.'" name="'.$fieldName.'[]"><a class="ccm-attribute-key-category-item-list-clear" href="javascript:void(0)"><img width="16" height="16" class="ccm-attribute-key-category-item-list-clear-button" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a></td></tr>';
				}
			}
		} else {
			$html .= '<tr class="ccm-attribute-key-category-selected-item-none-'.$akID.'"><td colspan="3">' . t('No items selected.') . '</td></tr>';
		}
		$html .= '</tbody></table><script type="text/javascript">
		$(function() {
			$("#ccmAttributeKeyCategoryItemSelect' . $akID . ' .ccm-attribute-key-category-select-item").dialog();
			$("a.ccm-attribute-key-category-item-list-clear").click(function() {
				$(this).parent().parent().remove();
				ccm_setupGridStriping(\'ccmAttributeKeyCategoryItemSelect' . $akID . '\');
			});
		});
		
		ccm_triggerSelectAttributeKeyCategoryItem = function(akID, that) {
			$("tr.ccm-attribute-key-category-selected-item-none-"+akID).hide();
			val = that.children(\':first-child\').find(\'input[name=ID]\').val();
			if ($("#ccmAttributeKeyCategoryItemSelect"+akID+"_" + val).length < 1) {
				html = \'<input type="hidden" value="\'+val+\'" name="\'+that.attr(\'fieldName\')+\'"><a class="ccm-attribute-key-category-item-list-clear" href="javascript:void(0)"><img width="16" height="16" class="ccm-attribute-key-category-item-list-clear-button" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a>\';
				that.children(":first-child").remove();
				that.children(":last-child").append(html);
				that.children(":last-child").attr("align", "center");
				that.children().removeAttr("onclick");
				that.attr("id", "ccmAttributeKeyCategoryItemSelect"+akID+"_" + val);
				$("#ccmAttributeKeyCategoryItemSelect"+akID+"_body").append(that);
			}
			
			ccm_setupGridStriping(\'ccmAttributeKeyCategoryItemSelect"+akID+"\');
			$("a.ccm-attribute-key-category-item-list-clear").click(function() {
				$(this).parent().parent().remove();
				ccm_setupGridStriping(\'ccmAttributeKeyCategoryItemSelect"+akID+"\');
			});
		}
		</script>
		<script type="text/javascript">
			ccm_setupAttributeKeyCategoryItemSearch(\'akID_'.$akID*time().'\', '.$akID.');
		</script>';	
		return $html;
	}
	
	
}