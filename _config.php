<?php
URLVariableToolController::add_urlvariable_panel();
DashboardLogController::add_log_panel();

SS_Log::add_writer(DashboardLogWriter::get_log_writer('SS_Log'));

//Examples:
//DashboardLog::log('Hello World from Dashboard');
//SS_Log::get_logger()->log('Hello World from SS_Log', SS_Log::NOTICE);
//Next lines require rewritten version of dev/Debug from branch debug_rewrite
//Debug::replaceDefaultOutputWriter(DashboardLogWriter::get_log_writer('DEBUG'));
//Debug::show('Hello World!');