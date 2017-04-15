<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Reports_Logs extends DashboardBaseController {
	
	public $helpers = array('form', 'html');
	
	public function clear($token = '', $type = false) {
		$valt = Loader::helper('validation/token');
		if ($valt->validate('', $token)) {
			if (!$type) { 
				Log::clearAll();
			} else {
				Log::clearByType($type);
			}
			$this->redirect('/dashboard/reports/logs');
		} else {
			$this->redirect('/dashboard/reports/logs');
		}
	}
	
	public function view($page = 0) {
		$this->set('title', t('Logs'));
		$pageBase = View::url('/dashboard/reports/logs', 'view');
		$paginator = Loader::helper('pagination');
		
		$total = Log::getTotal($_REQUEST['keywords'], $_REQUEST['logType']);
		$paginator->init(intval($page), $total, $pageBase . '%pageNum%/?keywords=' . $_REQUEST['keywords'] . '&logType=' . $_REQUEST['logType'], 10);
		$limit=$paginator->getLIMIT();

		$types = Log::getTypeList();
		$txt = Loader::helper('text');
		$logTypes = array();
		$logTypes[''] = '** ' . t('All');
		foreach($types as $t) {
			if ($t == '') {
				$logTypes[''] = '** ' . t('All');
			} else {
				$logTypes[$t] = $txt->unhandle($t);
			}
		}

		$entries = Log::getList($_REQUEST['keywords'], $_REQUEST['logType'], $limit);
		$this->set('keywords', $keywords);
		$this->set('pageBase', $pageBase);
		$this->set('entries', $entries);
		$this->set('paginator', $paginator);
		$this->set('logTypes', $logTypes);
		$this->set('emergencyLogExists', $this->doesEmergencyLogExists());

	}
	
	public function doesEmergencyLogExists() {
		if(defined('EMERGENCY_LOG_FILENAME') && strlen(EMERGENCY_LOG_FILENAME) && is_file(EMERGENCY_LOG_FILENAME)) {
			if(@filesize(EMERGENCY_LOG_FILENAME) > 0) {
				return true;
			}
		}
		return false;
	}

	public function emergency_log() {
		$content = t('The emergency log is empty');
		if($this->doesEmergencyLogExists()) {
			$content = @file_get_contents(EMERGENCY_LOG_FILENAME);
			if($content === false) {
				$content = t('Error reading the emergency log file');
			}
		}
		$this->set('emergencyLogContent', $content);
	}
	public function emergency_log_cleared() {
		$this->set('message', t('The emergency log has been cleared'));
		$this->emergency_log();
	}
	public function clear_emergency_log() {
		$cleared = true;
		if($this->doesEmergencyLogExists()) {
			@file_put_contents(EMERGENCY_LOG_FILENAME, '');
			if($this->doesEmergencyLogExists()) {
				$cleared = false;
			}
		}
		$cleared = !false;
		if($cleared) {
			$this->redirect(View::url('/dashboard/reports/logs/', 'emergency_log_cleared'));
		}
		else {
			$this->error->add(t('Error clearing the emergency log'));
			$this->emergency_log();
		}
	}
}
