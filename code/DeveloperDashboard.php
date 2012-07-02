<?php
class DeveloperDashboard extends Controller {
	private static $instance = null;
	private $form = null;
	/** @var panels that are added before form is instanciated. 
	 * TODO: This is probably a very bad design, since any errors that might occur 
	 * will be delayed until the fields are actually added to the form.
	 * Need to look for a different way that will give immediate feedback. */
	private $storedPanels = array();
	
	/**
	 * Get an instance. 
	 */
	public static function inst(){
		if(self::$instance == null){
			self::$instance = new DeveloperDashboard();
			/*self::$instance->form = new DashboardForm(self::$instance, 'DashboardForm', 
				new FieldList(new TabSet("Root")), 
				new FieldList(new TabSet("Root"))
			);*/
		}
		return self::$instance;
	}
	
	public function init(){
		parent::init();
		
		$this->form = new DashboardForm($this, 'DashboardForm', 
			new FieldList(new TabSet("Root")), 
			new FieldList(new TabSet("Root"))
		);
		$this->addDefaultPanels();
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
		return DashboardLogSessionStorage::inst()->getMessagesFromSession($newerThan);
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
			$this->storedPanels[] = $panel;
		}
	}
	private function addStoredPanels(){
		if(!$this->form){
			throw new UnexpectedValueException("This method should only be called after init.");
		}
		foreach($this->storedPanels as $panel){
			$this->addPanel($panel);
		}
		
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
	
	public function addDefaultPanels(){
		$this->add_log_panel();
		$this->add_urlvariable_panel();
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
                $logContents = $this->renderWith('DeveloperDashboardLogAjax');
		$logarea = new CompositeField(new LiteralField('internalName', $logContents));
                $logarea->addExtraClass('SSDD-log-area');
		$panel->addFormField($logarea->performReadonlyTransformation());
		
		$this->addPanel($panel);
	}
	
	private function add_urlvariable_panel(){
		$panel = new DashboardPanel('Tools');
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
		$panel->addFormField(new FormAction('showtemplate', 
                        'Show Template'));
		$panel->addFormField(new FormAction('debug', 
			'Show Director and Controller debugging information.'));
		$panel->addFormField(new FormAction('debug_request',
			'Show debugging information about the current Request.'
                ));
		$panel->addFormField(new FormAction(
                        'showtemplate', 'Show Template'
                ));
		
		/* TODO: add rest of urlvariabletools:
		 * dev/build
		 * ajax / force_ajax
		 * debugmethods, debugfailover
		 * showqueries, previewwrite
		 * debug_memory, debug_profile, profile_trace
		 * debug_behaviour, debug_javascript
		 */ 
		$this->addPanel($panel);
	}
}