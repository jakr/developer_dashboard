<?php
class DeveloperDashboard extends Controller {
	public static $url_handlers = array(
		'DeveloperDashboard/Log/$StartFrom' => 'GetLoggedData'
	);

	public function GetLoggedData($request, $StartFrom=0){
		echo $StartFrom;
		return DashboardLogWriter::getSavedMessages($StartFrom);
	}
}