<?php
class DashboardLogController extends Controller  implements DashboardPanelContentProvider {
	public static function add_log_panel(){
		$dlc = new DashboardLogController();
		$dlc->addLogPanel();
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
	public function getlog($request = null){
		if($request != null) {
			$this->request = $request;
		}
		return $this->renderWith('DeveloperDashboardLogAjax');
	}
	
	/**
	 * Called by the panel before it is displayed, adds the log data.
	 * @param DashboardPanel $panel
	 */
	public function getPanelContent(DashboardPanel $panel){
		$buttons = new CompositeField();
		$buttons->push(new AutomaticRefreshButton('getlog', 'Update'));
		$buttons->push(new JSOnlyButton('toggle_display_timestamp', 'Toggle Timestamps'));
		foreach(DashboardLogWriter::get_stream_ids() as $stream){
			$buttons->push(new DashboardStreamControlButton(
				$stream->StreamID,
				$stream->StreamID
			));
		}
		$buttons->addExtraClass('btn-toolbar');
		$panel->addFormField($buttons);
		$logContents = $this->getlog();
		
		$logarea = new CompositeField(new LiteralField('internalName', $logContents));
                $logarea->addExtraClass('SSDD-log-area');
		$panel->addFormField($logarea->performReadonlyTransformation());
	}
	
	private function addLogPanel(){
		$panel = new DashboardPanel('Logs');
		$panel->setContentProvider($this);
		$panel->forwardAction('getlog', $this);
		DeveloperDashboard::inst()->addPanel($panel);
		Requirements::css('developer_dashboard/css/ss_developer_dashboard_log.css');
		Requirements::javascript('developer_dashboard/javascript/dashboard_log.js');
	}
	
}
