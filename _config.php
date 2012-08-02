<?php
Director::set_environment_type('dev');

URLVariableToolController::add_urlvariable_panel();
DashboardLogController::add_log_panel();

SS_Log::add_writer(DashboardLogWriter::get_log_writer('SS_Log'));

if(strstr($_SERVER['REQUEST_URI'], 'getlog') === false
	&& strstr($_SERVER['REQUEST_URI'], 'admin/security/ping') === false
){
	DashboardLog::log($_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'], 'Request');
	if(Member::currentUserID()){
		DashboardLog::log('User ' . Member::currentUserID() . ' is logged in.', 'User');
	} else {
		DashboardLog::log('No user logged in.', 'User');
	}
}
//Examples:
//DashboardLog::log('Hello World from Dashboard');
//SS_Log::get_logger()->log('Hello World from SS_Log', SS_Log::NOTICE);
//Next lines require rewritten version of dev/Debug from branch debug_rewrite
//Debug::replaceDefaultOutputWriter(DashboardLogWriter::get_log_writer('DEBUG'));
//Debug::show('Hello World!');