<?php
class URLVariableToolController extends Controller {
	private static $action_names = array(
		'debug' => '1',
		'debug_request' => '1',
		'showtemplate' => '1',
		'build_classmanifest' => '/dev/build',
		'flush_all' => array('flush', 'all'),
		'flush_one' => array('flush', '1'),
	);
	private static $environment_types = array(
		'live' => 'Live', 
		'test' => 'Test', 
		'dev' => 'Development'
	);
	
	public static function add_urlvariable_panel() {
		$uvtc = new URLVariableToolController();
		$uvtc->addUrlvariablePanel();
	}
	
	public function redirectHandler($actionName, $request, $form) {
		if(!isset(self::$action_names[$actionName])){
			return;
		}
		$val = self::$action_names[$actionName];
		if(is_array($val) && count($val) == 2){
			$target = $this->getLastURLWithParameter(implode('=', $val));
		} elseif(substr($val, 0, 1) == '/' ){
			$target = Director::baseURL() . substr($val, 1);
		} else {
			$target = $this->getLastURLWithParameter("$actionName=$val");
		}
		return array('redirect' => $target);
	}
	
	public function flush_all($actionName, SS_HTTPRequest $request, $form) {
		return array('redirect' => $form->getController()->Link().'?flush=all');
	}
	
	private function getLastURL() {
		$lastURL = DeveloperDashboard::inst()->getLastURL();
		if($lastURL === false){
			$lastURL = Director::baseURL();
		}
		return $lastURL;
	}
	
	/**
	 * Append a get parameter (foo=bar) to $this->lastURL
	 * @param string $parameter
	 */
	private function getLastURLWithParameter($parameter) {
	
		$lastURL = $this->getLastURL();
		return $lastURL
			.(strstr($lastURL, '?') === false ? '?' : '&')
			.$parameter;
	}
	
	private function addUrlvariablePanel() {
		$panel = new DashboardPanel('Tools');
		
		//Global actions
		$panel->addFormField(new HeaderField('title-global-actions', 'Global Actions'));
		$panel->addFormField(
			new FormAction('flush_all', 'Flush template cache for all pages'),
			$this);
		$panel->addFormField(
			new FormAction('build_classmanifest','Rebuild class manifest (dev/build)'),
			$this, 'redirectHandler'
		);
		
		//Page level actions - only affect the "current" site
		$panel->addFormField(new HeaderField('title-pagelevel-actions', 'Page-Level Actions'));
		
		$panel->addFormField(
			new LabelField('Page affected', 
				'The follwoing commands will affect the page ' 
				. "{$this->getLastURL()}. <br />\n")
		);
		$panel->addFormField(
			new FormAction('flush_one', 'Flush template cache for this page'),
			$this, 'redirectHandler');
		$panel->addFormField(
			new FormAction('showtemplate', 'Show Template'),
			$this, 'redirectHandler'
		);
		$panel->addFormField(
			new FormAction('debug', 'Debug Director and Controller'),
			$this, 'redirectHandler'
		);
		$panel->addFormField(
			new FormAction('debug_request', 'Debug current Request.'),
			$this, 'redirectHandler'
		);
		
		/* TODO: add rest of urlvariabletools:
		 * dev/build
		 * ajax / force_ajax
		 * debugmethods, debugfailover
		 * showqueries, previewwrite
		 * debug_memory, debug_profile, profile_trace
		 * debug_behaviour, debug_javascript
		 */
		DeveloperDashboard::inst()->addPanel($panel);
		
	}
	
}