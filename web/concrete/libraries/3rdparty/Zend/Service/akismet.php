<?php





// Used by the Akismet class to communicate with the Akismet service
class Akismet {
    $fh=loader::helper('file');
    private $api_version = 1.1;
    private $urls;
    public $api_key='e8bf07b93f11';
    public $error = null;
    public $url = BASE_URL.view::url('/');
    
    function __construct($api_key=null, $site_url=null) {
        
        //Set the key to use
        if($api_key) {
            $this->api_key = $api_key;
        } else {
            trigger_error(t('Akismet API Key not set.'));
            $this->error = true;
        }
    }

    
    
    public function check_key() {
        $key=$fh->getContents('http://rest.akismet.com/'.$this->api_version.'/verify-key/?key='.$this->api_key.'&blog='.$this->url);
        if($key=='invalid'){
        	return false;
        }else{
        	return true;
        }
    }
    
    public function check(){
    	if(check_key()){
    		$badComment=$fh->getContents('http://'.$this->api_key.'.rest.akismet.com/'.$this->api_version.'/comment-check?blog='.$this->url.'&user_ip='.$_SERVER['REMOTE_ADDR'].'&user_agent='.$_SERVER['HTTP_USER_AGENT'].'&referrer='.$_SERVER['HTTP_REFERER'].'&comment_content='.$comment);
    		if($badComment){
    			return false;
    		}else{
    			return true;
    		}
    	}
    }
    
}

