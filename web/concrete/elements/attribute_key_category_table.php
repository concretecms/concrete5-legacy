<?php defined('C5_EXECUTE') or die(_("Access Denied.")); 
$txt = Loader::helper('text');
$akcsh = Loader::helper('attribute_key_category_settings');
?>

<?php if(is_array($pile)) { ?>
<table width="100%">
	<tr>
		<th align="left">Name</th>
		<th width="70px">Count</th>
		<th colspan="5" width="1%">Actions</th>
	</tr>
<?php foreach($pile as $akc) {
	$settings = $akc->getRegisteredSettings();
	if($settings['hidden']) continue;
	$html = '';
	foreach($akcsh->getActions() as $action) {
		if($hideAction[$action] || $settings['url_'.$action.'_hidden']) $hidden = TRUE;
		if($disableAction[$action] || $settings['url_'.$action.'_disabled']) $disabled = TRUE;
		$html .= '<td>';
		if(!$hidden) { 
			if(!$disabled) {
				if($settings['url_'.$action]) {
					$html .= '<a href="'.View::url($settings['url_'.$action]).'">';
					if($action == 'search') $search = View::url($settings['url_'.$action]);
					if($action == 'structure') $structure = View::url($settings['url_'.$action]);
				} else {
					$html .= '<a href="'.$linkPrefix.$action.'/'.$akc->getAttributeKeyCategoryHandle().$linkSuffix.'">';
					if($action == 'search') $search = $linkPrefix.$action.'/'.$akc->getAttributeKeyCategoryHandle().$linkSuffix;
					if($action == 'structure') $structure = $linkPrefix.$action.'/'.$akc->getAttributeKeyCategoryHandle().$linkSuffix;
				}
			}
			$html .= '<img src="'.$akcsh->getActionIconSrc($action).'" title="'.$txt->unhandle($action);
			if($disabled) $html .= ' Disabled';
			$html .= '" />';
			if(!$disabled) $html .= '</a>';
		}
		$html .= '</td>';
		unset($hidden); 
		unset($disabled);
	}
	$list = $akc->getItemList();
	$count = $list->getTotal();
	if($count > 0) {
		$name = '<a href="'.$search.'">'.$txt->unhandle($akc->getAttributeKeyCategoryHandle()).'</a>';
	} else {
		$name = '<a href="'.$structure.'">'.$txt->unhandle($akc->getAttributeKeyCategoryHandle()).'</a>';
	} ?>
	<tr>
		<td><h2><?=$name?></h2></td>
		<td align="center"><?=$count?></td>
		<?php print $html; ?>
	</tr>
<?php } ?>
<?php } ?>
</table>
