<?php
class URLVariableToolController extends Controller {
	private static $action_names = array(
		'debug' => '1',
		'debug_request' => '1',
		'showtemplate' => '1',
		'build_classmanifest' => '/dev/build',
	);
	
	public static function add_urlvariable_panel(){
		$uvtc = new URLVariableToolController();
		$uvtc->addUrlvariablePanel();
	}
	
	public function redirectHandler($actionName, $request, $form){
		echo $actionName;
		if(!isset(self::$action_names[$actionName])){
			return;
		}
		$val = self::$action_names[$actionName];
		if(substr($val, 0, 1) == '/' ){
			$target = Director::absoluteURL($val);
		} else {
			$target = $this->getLastURLWithParameter("$actionName=$val");
		}
		return array('redirect' => $target);
	}
	
	public function savesettings(){
		/*
		 * TODO implement storing and retreiving settings
		 * This could be used to set the site into dev mode,
		 *  or to append an URL paramter to any request.
		 */
		return 'Not implemented yet';
	}
	
	private function getLastURL(){
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
	private function getLastURLWithParameter($parameter){
	
		$lastURL = $this->getLastURL();
		return $lastURL
			.(strstr($lastURL, '?') === false ? '?' : '&')
			.$parameter;
	}
	
	private function addUrlvariablePanel(){
		$panel = new DashboardPanel('Tools');
		
		//Global settings
		$panel->addFormField(new HeaderField('title-global', 'Global Settings'));
		$panel->addFormField(new DropdownField('site-mode', 'Mode', 
			array('dev' => 'Development', 'test' => 'Test', '' => 'Live')
                ));
		$panel->addFormField(new DropdownField(
			'clear-cache', 
			'Flush template cache', 
			array(
				'' => '', 
				'all' => 'Complete cache', 
				'one' => 'Templates used on this page'
			)
		));
		$panel->addFormField(
			new FormAction('savesettings','Save Settings'),
			$this
		);
		$panel->addFormField(
			new FormAction('build_classmanifest','Rebuild class manifest (dev/build)'),
			$this, 'redirectHandler'
		);
		
		//Only affecting "current" site
		$panel->addFormField(new HeaderField('title-pagelevel', 'Page-level Settings'));
		
		$panel->addFormField(
			new LabelField('Page affected', 
				'The follwoing commands will affect the page ' 
				. "{$this->getLastURL()}. <br />\n")
		);
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
        // TODO: Make use of callbacks.
		DeveloperDashboard::inst()->addPanel($panel);
		
	}
	
}