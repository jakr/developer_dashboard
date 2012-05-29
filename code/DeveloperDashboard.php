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
			$ret .= "==== $requestNr ====\n";
			foreach($data as $datum){
				$ret .= "$datum \n";
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
	
	public function init(){
		parent::init();
		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
		Requirements::javascript('developer_dashboard/javascript/dashboard_detached.js');
	}
}