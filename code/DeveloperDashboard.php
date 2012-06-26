<?php
class DeveloperDashboard extends Controller {
	private static $tabs = array();
	private static $form = null;
	
	public static function add_panel($panel){
		self::$form->add_panel($panel);
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
		
		if(self::$form == null){
			self::$form = new DashboardForm($this, 'DashboardForm', 
				new FieldList(new TabSet("Root")), 
				new FieldList(new TabSet("Root"))
			);
		}
		
		self::add_log_panel();
		self::add_urlvariable_panel();
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
	
	public function DashboardForm(){
		return self::$form;
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
	
	private function add_log_panel(){
		$fieldList = new FieldList();
		$fieldList->add(new AutomaticRefreshButton('getlog', 'Update'));
		foreach(DashboardLogWriter::get_stream_ids() as $stream){
			$fieldList->add(new DashboardStreamControlButton(
				$stream->StreamID,
				$stream->StreamID
			));
		}
		
		self::add_panel(new DashboardPanel('Logs', $fieldList));
	}
	
	private function add_urlvariable_panel(){
		self::add_panel(new DashboardPanel('Tools', 
			new FieldList(
				new TextField("test")
			)
		));
	}
}