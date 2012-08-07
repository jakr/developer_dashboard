<?php
URLVariableToolController::add_urlvariable_panel();
DashboardLogController::add_log_panel();
DashboardLogFileController::add_log_file_panel();
SS_Log::add_writer(DashboardLogWriter::get_log_writer('SS_Log'));

//Examples:
//DashboardLog::log('Hello World from Dashboard');
//SS_Log::get_logger()->log('Hello World from SS_Log', SS_Log::NOTICE);

//Next lines require rewritten version of dev/Debug from branch debug_rewrite
//$writers = DashboardLog::get_log_wrapper('DEBUG', true)->logger->getWriters();
//Debug::replaceDefaultOutputWriter($writers[0]);
//Debug::show('Hello World!');