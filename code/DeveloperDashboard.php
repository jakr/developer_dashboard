<?php
class DeveloperDashboard extends Controller {
	private static $tabs = array();
	
	public static function addTab($tab){
		self::$tabs[$tab->tabID] = $tab;
	}
	
	public function init(){
		parent::init();
		Requirements::css(FRAMEWORK_DIR.'/admin/css/screen.css');
		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.min.js');
		Requirements::javascript('developer_dashboard/thirdparty/bootstrap/js/bootstrap.min.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		//Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.min.js');
		Requirements::css(FRAMEWORK_DIR.'/thirdparty/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::css('developer_dashboard/thirdparty/bootstrap/css/bootstrap.min.css');
		Requirements::css('developer_dashboard/css/ss_developer_dashboard.css');
		
		$urlvarTab = new DashboardTab('URL Variable Tools', 'urlvariabletools');
		self::addTab($urlvarTab);
	}
	
	public function GetLoggedData(){
		$param = $this->request->latestParam('ID');
		$newerThan = $param === null ? 0 : $param;
		return DashboardLogWriter::get_messages_from_session($newerThan);
	}
	
	//@TODO @Mark: There is possibly a smarter way to do this. 
	public function Tabs(){
		$ret = new ArrayList();
		foreach(self::$tabs as $tab){
			$ret->push(new ArrayData(array(
				'Title'=> $tab->Title,
				'ID'=> $tab->ID,
				'Content'=> $tab->getContent()
			)));
		}
		return $ret;
	}
	
	public function HasMultipleTabs(){
		//At the moment, the log tab is hardcoded.
		return count(self::$tabs) >= 1;
	}
	
	/**
	 * This is an action controller that returns the newest log messages.
	 * It gets called via AJAX.
	 * 
	 */
	public function getlog(){
		return $this->renderWith('DeveloperDashboardLogAjax');
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
}