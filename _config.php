<?php
Director::set_environment_type('dev');

URLVariableToolController::add_urlvariable_panel();
DashboardLogController::add_log_panel();

//DEBUG: Call the log writer every time this file is loaded
//This makes sure that every request generates at least one log message
DashboardLog::log($_SERVER['REQUEST_METHOD'].' '.$_SERVER['REQUEST_URI'], 'Request');
if(Member::currentUserID()){
	DashboardLog::log('User ' . Member::currentUserID() . ' is logged in.', 'User');
} else {
	DashboardLog::log('No user logged in.', 'User');
}
DashboardLog::log('Hello World from Dashboard');
DashboardLog::log('A Message in a different stream', 'SomeOtherStream');
