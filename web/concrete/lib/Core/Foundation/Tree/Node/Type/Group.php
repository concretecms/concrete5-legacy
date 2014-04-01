<?
namespace Concrete\Core\Foundation\Tree\Node\Type;
use Concrete\Core\Foundation\Tree\Node\Node;
use Loader;
class Group extends Node {

	public function getTreeNodePermissionKeyCategoryHandle() { return 'group_tree_node';}
	public function getTreeNodeGroupID() {
		return $this->gID;
	}
	public function getTreeNodeGroupObject() {
		return Group::getByID($this->gID);
	}
	public function getTreeNodeDisplayName() {
		if ($this->treeNodeParentID == 0) {
			return t('All Groups');
		}

		$g = Group::getByID($this->gID);
		return t($g->getGroupName());
	}

	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select * from TreeGroupNodes where treeNodeID = ?', array($this->treeNodeID));
		$this->setPropertiesFromArray($row);
	}

	public function move(TreeNode $newParent) {
		parent::move($newParent);
		$g = $this->getTreeNodeGroupObject();
		if (is_object($g)) {
			$g->rescanGroupPathRecursive();
		}
	}

	public static function getTreeNodeByGroupID($gID) {
		$db = Loader::db();
		$treeNodeID = $db->GetOne('select treeNodeID from TreeGroupNodes where gID = ?', array($gID));
		if ($treeNodeID) {
			$tn = TreeNode::getByID($treeNodeID);
			return $tn;
		}
	}

	public function deleteDetails() {
		$db = Loader::db();
		$db->Execute('delete from TreeGroupNodes where treeNodeID = ?', array($this->treeNodeID));
	}

	public function getTreeNodeJSON() {
		$obj = parent::getTreeNodeJSON();
		if (is_object($obj)) {
			$obj->gID = $this->gID;
			$obj->icon = ASSETS_URL_IMAGES . '/icons/group.png';
			return $obj;
		}
	}

	public function setTreeNodeGroup(Group $g) {
		$db = Loader::db();
		$db->Replace('TreeGroupNodes', array('treeNodeID' => $this->getTreeNodeID(), 'gID' => $g->getGroupID()), array('treeNodeID'), true);
		$this->gID = $g->getGroupID();
	}

	public static function add($group = false, $parent = false) {
		$db = Loader::db();
		$node = parent::add($parent);
		if (is_object($group)) {
			$node->setTreeNodeGroup($group);
		}
		return $node;
	}

}
