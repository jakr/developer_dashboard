<?php
class DeveloperDashboard extends Controller {
	private static $instance = null;
	private $form = null;
	/** @var panels that are added before form is instanciated.*/
	private static $storedPanels = array();
	
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
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		//Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.min.js');
		Requirements::css(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::css('developer_dashboard/thirdparty/bootstrap/css/bootstrap.min.css');
		Requirements::css('developer_dashboard/css/ss_developer_dashboard.css');
		
	}
	
	public function GetLoggedData(){
		$param = $this->request->latestParam('ID');
		$newerThan = $param === null ? 0 : $param;
		return DashboardSessionStorage::inst()->getMessagesFromSession($newerThan);
	}
	
	/**
	 * This is an action controller that returns the newest log messages.
	 * It gets called via AJAX.
	 * 
	 */
	public function getlog(){
		return $this->renderWith('DeveloperDashboardLogAjax');
	}
	
	public function addPanel($panel){
		if($this->form){
			$this->form->addPanel($panel);
		} else {
			self::$storedPanels[] = $panel;
		}
	}
	private function addStoredPanels(){
		if(!$this->form){
			throw new UnexpectedValueException("This method should only be called after init.");
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
		//TODO we have to store the last valid referer, maybe in the session.
		if(isset($_SERVER['HTTP_REFERER']) 
			&& $_SERVER['HTTP_REFERER'] != Director::absoluteURL($this->Link())
		){
			return $_SERVER['HTTP_REFERER'];
		} else {
			return false;
		}
	}
}