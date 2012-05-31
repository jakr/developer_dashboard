<?php
class DeveloperDashboard extends Controller {
	
	public function GetLoggedData(){
		$param = $this->request->latestParam('ID');
		$newerThan = $param === null ? 0 : $param;
		return DashboardLogWriter::getMessagesFromSession($newerThan);
	}
	
	public function GetLog($request){
		return $this->renderWith('DeveloperDashboardLogAjax');
	}
	
	public function GetStreams(){
		return DashboardLogWriter::getStreamIDs();
	}
	
	public function init(){
		parent::init();
		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		Requirements::css('framework/admin/css/screen.css');
		Requirements::css('developer_dashboard/css/ss_developer_dashboard.css');
	}
}