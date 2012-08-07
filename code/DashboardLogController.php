<?php
/**
 * The Controller for the dashboard log pabel.
 */
class DashboardLogController extends Controller implements DashboardPanelContentProvider {
	/**
	 * Add the log panel to the Dashboard.
	 */
	public static function add_log_panel(){
		$dlc = new DashboardLogController();
		$dlc->addLogPanel();
	}
	
	/**
	 * Get log data stored in the session.
	 * If a parameter is supplied via the URL, only requests newer than that
	 *  are returned.
	 * @return ArrayList
	 */
	public function GetLoggedData(){
		$param = $this->request->latestParam('ID');
		$newerThan = $param === null ? 0 : $param;
		return DashboardSessionStorage::inst()->getMessagesFromSession($newerThan);
	}
	
	/**
	 * This is an action controller that returns the newest log messages.
	 * It gets called via AJAX.
	 * @return string the HTML.
	 */
	public function getlog($request = null){
		if($request != null) {
			$this->request = $request;
		}
		return $this->renderWith('DeveloperDashboardLogAjax');
	}
	
	/**
	 * Get a button to control the display of the stream specified by the 
	 *  URL parameter.
	 * @param SS_HTTPRequest $request
	 * @return string the HTML
	 */
	public function getstreambutton(SS_HTTPRequest $request = null){
		if($request == null) return;
		$ad = new ArrayData(array('Title'=>$request->latestParam('ID')));
		return $ad->renderWith('DashboardStreamControlButton');
	}
	
	/**
	 * Called by the panel before it is displayed, adds the log data.
	 * @param DashboardPanel $panel
	 */
	public function getPanelContent(DashboardPanel $panel){
		$controls = new CompositeField();
		$controls->push(new AutomaticRefreshButton('getlog', 'Update'));
		$controls->push(new JSOnlyButton('toggle_display_timestamp', 'Toggle Timestamps'));
		$requests = array('all' => 'All');
		for($i=1; $i <= DashboardSessionStorage::$requests_to_keep; $i++){
			$requests[$i] = $i;
		}
		$controls->push(new DropdownField('show_last_requests', 
				'Number of Requests to display', $requests, 'all'));
		foreach(DashboardLogWriter::get_stream_ids() as $stream){
			$controls->push(new DashboardStreamControlButton(
				$stream->StreamID,
				$stream->StreamID
			));
		}
		$controls->addExtraClass('btn-toolbar SSDD-log-stream-visibility-buttons');
		$panel->addFormField($controls);
		$logContents = $this->getlog();
		
		$logarea = new CompositeField(new LiteralField('internalName', $logContents));
                $logarea->addExtraClass('SSDD-log-area');
		$panel->addFormField($logarea->performReadonlyTransformation());
	}
	
	/**
	 * Does the work for adding the log panel to the Dashboard.
	 */
	private function addLogPanel(){
		$panel = new DashboardPanel('Logs');
		$panel->setContentProvider($this);
		$panel->forwardAction('getlog', $this);
		$panel->forwardAction('getstreambutton', $this);
		DeveloperDashboard::inst()->addPanel($panel);
		Requirements::css('developer_dashboard/css/ss_developer_dashboard_log.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.min.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_log.js');
	}
	
}
