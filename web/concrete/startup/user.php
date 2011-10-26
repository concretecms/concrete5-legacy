<?
	defined('C5_EXECUTE') or die("Access Denied.");
	if (!User::isLoggedIn()) {
		User::checkUserForeverCookie();
	}
	
	if (User::isLoggedIn()) {		
		// check to see if this is a valid user account
		$u = new User();
		$loggedIn = $u->checkLogin();
		if ($loggedIn !== true) {
			$u->logout();
			$v = View::getInstance();
			$v->setTheme(VIEW_CORE_THEME);
			if($loggedIn == USER_SESSION_DENIED) {
				Loader::controller('/login')->redirect("/login", "session_denied");		
			} elseif($loggedIn == USER_INVALID || !$u->isActive()) {
				Loader::controller('/login')->redirect("/login", "account_deactivated");		
			} else {
				$v->render("/user_error");		
			}
		}
	}
