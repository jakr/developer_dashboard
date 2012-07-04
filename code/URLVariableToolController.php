<?php
class URLVariableToolController extends Controller {
	public function __construct(){
		parent::__construct();
		$this->add_urlvariable_panel();
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
                ), $this);
		
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
	
	public function showtemplate(){
		echo "Called";
	}
	
}