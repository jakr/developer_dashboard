<?php
Director::set_environment_type('dev');
//DEBUG: This call creates an instance and might help to expose errors
// that result from the assumption that init() is called first.
DeveloperDashboard::inst();
//DEBUG: Call the log writer every time this file is loaded
//This makes sure that every request generates at least one log message
DashboardLog::log('Hello World from Dashboard');
DashboardLog::log('A Message in a different stream', 'SomeOtherStream');

//DeveloperDashboard::addTab(new DeveloperDashboardToolbar());