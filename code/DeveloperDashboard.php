<?php
class DeveloperDashboard extends Controller {
	
	private static $instance = null;
	/** @var DashboardForm the form */
	private $form = null;
	/** @var panels that are added before form is instanciated.*/
	private static $storedPanels = array();
	/** @var array the actions that should be forwarded.*/
	private static $actions = array();
	
	/**
	 * Get an instance. 
	 * @return DeveloperDashboard the instance
	 */
	public static function inst(){
		if(self::$instance == null){
			self::$instance = new DeveloperDashboard();
			//$form is not instanciated yet! Will be instanciated during init().
		}
		return self::$instance;
	}
	
	public function init(){
		parent::init();
		
		$this->form = new DashboardForm($this, 'DashboardForm', 
			new FieldList(new TabSet("Root")), 
			new FieldList(new TabSet("Root"))
		);
		$this->addStoredPanels();
		
		Requirements::css(FRAMEWORK_DIR.'/admin/css/screen.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.min.js');
		Requirements::javascript('developer_dashboard/thirdparty/bootstrap/js/bootstrap.min.js');
		//@TODO: Rename JS file and move next line to DashboardLogController->init.
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		//Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.min.js');
		Requirements::css(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::css('developer_dashboard/thirdparty/bootstrap/css/bootstrap.min.css');
		Requirements::css('developer_dashboard/css/ss_developer_dashboard.css');
		
	}

	public function handleAction($request){
		//Setting $allowed_actions would not account for those added by others.
		if(Director::get_environment_type() != 'dev'){
			return __class__.' can only be used in development mode by Administrators.';
		}
		if(Permission::check('ADMIN') === false){
			return __class__.' can only be used by Administrators.';
		}
		
		$action = $request->latestParam('Action');
		if(isset(self::$actions[$action])){
			$controller = self::$actions[$action][0];
			$method = self::$actions[$action][1];
			return $controller->$method($request);
		} else {
			return parent::handleAction($request);
		}
	}
	
	public function __destruct(){
		if(Director::get_environment_type() == 'dev' 
			&& !Director::is_ajax()
			// it is too late to use SapphireTest::is_running_test()
			&& strstr($_SERVER['REQUEST_URI'], '/dev/tests/') === false
			&& Permission::check('ADMIN') //Required DB connection missing during tests!
		){
			echo '<div><a href="'.Director::absoluteURL($this->Link())
					.'">Open Developer Dashboard</a></div>';
		}
	}
	
	public function addPanel(DashboardPanel $panel){
		if($this->form){
			$this->form->addPanel($panel);
		} else {
			self::$storedPanels[] = $panel;
		}
		foreach($panel->Actions() as $actionName => $callbackInfo){
			self::$actions[$actionName] = $callbackInfo;
		}
	}
	private function addStoredPanels(){
		if(!$this->form){
			throw new UnexpectedValueException(
				"This method should only be called after init.");
		}
		foreach(self::$storedPanels as $panel){
			$panel->updateContent();
			$this->form->addPanel($panel);
		}
		self::$storedPanels = array(); //clear stored panels
	}
	
	public function DashboardForm(){
		return $this->form;
	}
	
	/**
	 * Get the available streams (=stream ids).
	 * @see DashboardLogWriter::get_stream_ids()
	 * 
	 * @return ArrayList
	 */
	public function GetStreams(){
		return DashboardLogWriter::get_stream_ids();
	}
	
	/**
	 * Get the URL that was visited last (see the php doc for HTTP_REFERER).
	 * 
	 * If the previous page cannot be determined or if it was this Controller,
	 *  false is returned.
	 * @return mixed string the URL or boolean false.
	 */
	public function getLastURL(){
		$storage = DashboardSessionStorage::inst();
		if(isset($_SERVER['HTTP_REFERER']) 
			&& $_SERVER['HTTP_REFERER'] != Director::absoluteURL($this->Link())
		){
			$storage->storeSetting(
					'ORIGINAL_HTTP_REFERER', $_SERVER['HTTP_REFERER']);
			return $_SERVER['HTTP_REFERER'];
		} else {
			return $storage->loadSetting('ORIGINAL_HTTP_REFERER');
		}
	}
}