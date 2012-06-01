<?php
Director::set_environment_type('dev');
//DEBUG: Call the log writer every time this file is loaded
//This makes sure that every request generates at least one log message
DashboardLog::log('Hello World from Dashboard');
DashboardLog::log('A Message in a different stream', 'SomeOtherStream');

//DeveloperDashboard::addTab(new DeveloperDashboardToolbar());