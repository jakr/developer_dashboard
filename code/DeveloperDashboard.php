<?php
/**
 * The main class for the developer dashboard.
 * 
 * This class provides the view of the dashboard at baseURL/DeveloperDashboard.
 * To add a panel / tab to the dashboard, call addPanel().
 * @see DashboardPanel
 * 
 * It is also responsible for displaying the "Open Dashboard" link.
 *  This is done in __destruct.
 */
class DeveloperDashboard extends Controller {
	
	/** @var DeveloperDashboard the singleton instance.*/
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
		
		Requirements::css(FRAMEWORK_DIR.'/admin/css/screen.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.min.js');
		Requirements::javascript('developer_dashboard/thirdparty/bootstrap/js/bootstrap.min.js');
		Requirements::css('developer_dashboard/thirdparty/bootstrap/css/bootstrap.min.css');
		Requirements::javascript('developer_dashboard/javascript/dashboard.js');
		
		$this->form = new DashboardForm($this, 'DashboardForm', 
			new FieldList(new TabSet("Root")), 
			new FieldList(new TabSet("Root"))
		);
		$this->addStoredPanels();
		
	}

	/**
	 * Overwrites the default action handler in Controller in order to forward
	 *  actions that were added to the panels.
	 * 
	 * @param type $request
	 * @return mixed
	 */
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
	
	/**
	 * Destructor.
	 * 
	 * Displays the "Open Dashboard" link.
	 */
	public function __destruct(){
		if(Director::get_environment_type() == 'dev' 
			&& !Director::is_ajax()
			// it is too late to use SapphireTest::is_running_test()
			&& strstr($_SERVER['REQUEST_URI'], '/dev/tests/') === false
			&& Permission::check('ADMIN') //Required DB connection missing during tests!
			&& strstr($_SERVER['REQUEST_URI'], $this->Link()) === false
		){
			echo '<div><a href="'.Director::absoluteURL($this->Link())
					.'">Open Developer Dashboard</a></div>';
		}
	}
	
	/**
	 * Add a panel. @see DashboardPanel for more information.
	 * @param DashboardPanel $panel
	 */
	public function addPanel(DashboardPanel $panel){
		self::$storedPanels[] = $panel;
		foreach($panel->Actions() as $actionName => $callbackInfo){
			self::$actions[$actionName] = $callbackInfo;
		}
	}

	/**
	 * Internal function used to add the panels to the form after it was
	 *  initialized.
	 * @throws UnexpectedValueException if $this->form was not initialized.
	 */
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
	
	/**
	 * Get the form.
	 * @return DashboardForm
	 */
	public function DashboardForm(){
		return $this->form;
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