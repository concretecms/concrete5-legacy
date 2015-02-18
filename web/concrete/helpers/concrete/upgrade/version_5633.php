<?php defined('C5_EXECUTE') or die("Access Denied.");

class ConcreteUpgradeVersion5633Helper {

	public $dbRefreshTables = array(
		'QueueMessages'
	);

	public function run() {
		if (!ENABLE_USER_PROFILES) {
			$membersPage = Page::getByPath('/members');
			if ($membersPage instanceof Page && !$membersPage->isError()) {
				$membersPage->delete();
			}
		}
		
		// Update robots.txt
		
		$delete_rules = array(
			'Disallow: /blocks',
			'Disallow: /concrete',
			'Disallow: /css',
			'Disallow: /js',
			'Disallow: /themes',
			'Disallow: /packages',
			'Disallow: /updates'
		);
		
		$add_rules = array(
			'Disallow: /blocks/*.php$',
			'Disallow: /blocks/*.xml$',
			'Disallow: /concrete/*.php$',
			'Disallow: /concrete/*.xml$',
			'Disallow: /packages/*.php$',
			'Disallow: /packages/*.xml$',
			'Disallow: /updates/*.php$',
			'Disallow: /updates/*.xml$'
		);
		
		$robotspath = DIR_BASE . '/robots.txt';
		$fh = Loader::helper('file');
		
		if (file_exists($robotspath) && is_writable($robotspath)) {
			$rules = array();
			
			$robotstxt = @file($robotspath, FILE_IGNORE_NEW_LINES);
			foreach ($robotstxt as $line) {
				$line = trim($line);
				if (!in_array($line, $delete_rules)) {
					$rules[] = $line;
				}
			}
			
			$new_rules = array_merge($rules, $add_rules);
			$new_robotstxt = implode("\n", $new_rules);
			
			$fh->clear($robotspath);
			$fh->append($robotspath, $new_robotstxt);
		}
	}

}
