<?
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for choosing a ---- from the concrete5 sitemap tool.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */


/**
 * Modified to fit needs of the Virtual Tables package
 * @author Isaac Jessup <ibjessup@comcast.net>
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class FormAttributeKeyCategoryItemSelectorHelper {
	
	public function selectItems($akCategoryHandle, $fieldName, $values, $akID) {
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		$html = '';
		$html .= '<table width="100%" id="ccmUserSelect' . $akID . '" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">';
		$ak = new AttributeKey($akCategoryHandle);
		$hList = $ak->getColumnHeaderList($akCategoryHandle);
		if(is_array($rs['static_attributes'])) foreach($rs['static_attributes'] as $sa) {
			$html .= '<th>'.$sa.'</th>';
		}
		if(is_array($hList)) foreach($hList as $ak) { 
			$html .= '<th>'.$ak->getAttributeKeyName().'</th>';
		}
		$html .= '<th width="30px" align="right"><a class="ccm-user-select-item dialog-launch" onclick="ccmActiveUserField=this" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' . t('Choose Items') . '" href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/bricks/search/search_dialog?mode=choose_multiple&akCategoryHandle='.$akCategoryHandle.'&akID='.$akID.'"><img src="' . ASSETS_URL_IMAGES . '/icons/add.png" width="16" height="16" /></a></th>';
		$html .= '</tr><tbody id="ccmUserSelect' . $akID . '_body" >';
		if(!empty($values)) {
			foreach($values as $akci) {
				if($akci) {
					$html .= '<tr class="" id="ccmUserSelect'.$akID.'_'.$akci->ID.'">';
					if(is_array($rs['static_attributes'])) foreach($rs['static_attributes'] as $sa => $value) {
						$html .= '<td>'.$akci->$sa.'</td>';
					}
					if(is_array($hList)) foreach($hList as $ak) { 
						$html .= '<td>'.$akci->getAttribute($ak).'</td>';
					}
					$html .= '<td align="center"><input type="hidden" value="'.$akci->ID.'" name="'.$fieldName.'[]"><a class="ccm-user-list-clear" href="javascript:void(0)"><img width="16" height="16" class="ccm-user-list-clear-button" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a></td></tr>';
				}
			}
		} else {
			$html .= '<tr class="ccm-user-selected-item-none-'.$akID.'"><td colspan="3">' . t('No items selected.') . '</td></tr>';
		}
		$html .= '</tbody></table><script type="text/javascript">
		$(function() {
			$("#ccmUserSelect' . $akID . ' .ccm-user-select-item").dialog();
			$("a.ccm-user-list-clear").click(function() {
				$(this).parent().parent().remove();
				ccm_setupGridStriping(\'ccmUserSelect' . $akID . '\');
			});
		});
		
		ccm_triggerSelectNewObject'.$akID.' = function(that) {
			$("tr.ccm-user-selected-item-none-'.$akID.'").hide();
			val = that.children(\':first-child\').children(\':first-child\').val();
			if ($("#ccmUserSelect' . $akID . '_" + val).length < 1) {
				html = \'<input type="hidden" value="\'+val+\'" name="'.$fieldName.'[]"><a class="ccm-user-list-clear" href="javascript:void(0)"><img width="16" height="16" class="ccm-user-list-clear-button" src="' . ASSETS_URL_IMAGES . '/icons/close.png"></a>\';
				that.children(":first-child").remove();
				that.children(":last-child").append(html);
				that.children(":last-child").attr("align", "center");
				that.children().removeAttr("onclick");
				that.attr("id", "ccmUserSelect' . $akID . '_" + val);
				$("#ccmUserSelect' . $akID . '_body").append(that);
			}
			
			ccm_setupGridStriping(\'ccmUserSelect' . $akID . '\');
			$("a.ccm-user-list-clear").click(function() {
				$(this).parent().parent().remove();
				ccm_setupGridStriping(\'ccmUserSelect' . $akID . '\');
			});
		}
		</script>';	
		return $html;
	}
	
	
}