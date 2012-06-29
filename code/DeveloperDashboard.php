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
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.min.js');
		Requirements::javascript('developer_dashboard/thirdparty/bootstrap/js/bootstrap.min.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		//Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.min.js');
		Requirements::css(THIRDPARTY_DIR.'/jquery-ui-themes/smoothness/jquery-ui.css');
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
		$buttons = new CompositeField();
		$buttons->push(new AutomaticRefreshButton('getlog', 'Update'));
		foreach(DashboardLogWriter::get_stream_ids() as $stream){
			$buttons->push(new DashboardStreamControlButton(
				$stream->StreamID,
				$stream->StreamID
			));
		}
		$buttons->addExtraClass('btn-toolbar');
		$panel = new DashboardPanel('Logs');
		$panel->addFormField($buttons);
		$logarea = new TextareaField('SSDD-log-area');
		$panel->addFormField($logarea->performReadonlyTransformation());
		
		self::add_panel($panel);
	}
	
	private function add_urlvariable_panel(){
		$panel = new DashboardPanel('Tools');
		$panel->addFormField(new DropdownField('site-mode', 'Mode', 
			array('dev' => 'Development', 'test' => 'Test', '' => 'Live')));
		$panel->addFormField(new DropdownField(
			'clear-cache', 
			'Flush template cache', 
			array(
				'' => '', 
				'all' => 'Complete cache', 
				'one' => 'Templates used on this page'
			)
		));
		$panel->addFormField(new FormAction('showtemplate', 'Show Template'));
		$panel->addFormField(new FormAction('debug', 
			'Show debugging information about Director and Controller'));
		$panel->addFormField(new FormAction('debug_request',
			'Show debugging information about the current Request.'));
		$panel->addFormField(new FormAction('showtemplate', 'Show Template'));
		
		/* TODO: add rest of urlvariabletools:
		 * dev/build
		 * ajax / force_ajax
		 * debugmethods, debugfailover
		 * showqueries, previewwrite
		 * debug_memory, debug_profile, profile_trace
		 * debug_behaviour, debug_javascript
		 */ 
		self::add_panel($panel);
	}
}