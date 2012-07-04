<?php
class DashboardLogController extends Controller {
	public function __construct(){
		parent::__construct();
		$this->add_log_panel();
	}
	
	private function add_log_panel(){
		$panel = new DashboardPanel('Logs');
		$panel->setFormContentCallback($this);
		DeveloperDashboard::inst()->addPanel($panel);
	}
	
	/**
	 * Called by the panel before it is displayed, adds the log data.
	 * @param DashboardPanel $panel
	 */
	public function getPanelContent(DashboardPanel $panel){
		$buttons = new CompositeField();
		$buttons->push(new AutomaticRefreshButton('getlog', 'Update'));
		foreach(DashboardLogWriter::get_stream_ids() as $stream){
			$buttons->push(new DashboardStreamControlButton(
				$stream->StreamID,
				$stream->StreamID
			));
		}
		$buttons->addExtraClass('btn-toolbar');
		$panel->addFormField($buttons);
		$logContents = DeveloperDashboard::inst()->getlog();
		
		$logarea = new CompositeField(new LiteralField('internalName', $logContents));
                $logarea->addExtraClass('SSDD-log-area');
		$panel->addFormField($logarea->performReadonlyTransformation());		
	}
	
}
