<?

defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/login');

class Concrete5_Controller_PageForbidden extends LoginController {
	
	public function view() {
		$v = View::getInstance();
		$c = $v->getCollectionObject();
		if (is_object($c)) {
			$cID = $c->getCollectionID();

            // parameters can be in a friendly-URL format: /tool/property1Value/property2Value/
            // or simply naming each property: /tool/?property1=property1Value&property2=property2Value
            $parameters = false;
            $get = $this->get();
            if ( is_array( $v->controller->parameters )
                 && !empty( $v->controller->parameters )
            ) {
                $parameters = $v->controller->parameters;
            } else if ( !empty( $get )
            ) {
                $parameters = $get;
            }

			if($cID) { 
				$this->forward($cID, $parameters); // set the intended url
			}
		}
		parent::view();
		$u = new User();
		$logged = $u->isLoggedIn();
		if(!$logged && FORBIDDEN_SHOW_LOGIN) { //if they are not logged in, and we show guests the login...
			$this->render('/login');
		}
	}
	
}