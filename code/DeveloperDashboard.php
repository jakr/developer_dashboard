<?php
class DeveloperDashboard extends Controller {

	public function GetLoggedDataAsJS($newerThan = 0){
		return json_encode(DashboardLogWriter::getMessagesFromSession($newerThan));
	}
	
	public function GetLoggedData(){
		$messages = DashboardLogWriter::getMessagesFromSession();
		$ret = '';
		$js = '';
		foreach($messages as $requestNr => $data){
			$ret .= "==== $requestNr ====<br />\n";
			foreach($data as $datum){
				$ret .= "<span class=\"".$datum->streamID."\">$datum </span><br />";
			}
		}
		return $ret;
	}
	
	public function GetLog($request){
		$newerThan = $request->latestParam('ID'); 
		if($newerThan !== null){
			return $this->GetLoggedDataAsJS($newerThan);
		} else {
			return $this->GetLoggedData();
		}
	}
	
	public function GetStreams(){
		return DashboardLogWriter::getStreamIDs();
	}
	
	public function init(){
		parent::init();
		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
		Requirements::css('developer_dashboard/css/ss_developer_dashboard.css');
	}
}