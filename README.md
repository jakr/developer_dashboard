developer_dashboard
===================

## Installation ##
The dashboard works as a separate module. As with any other module, download the code, put it in a subfolder and call /dev/build to rebuild the class manifest.

The Toolbar is accessible under yoursite/DeveloperDashboard/

## Usage ##
Please note that the Dashboard is at an early stage of development and that the API still changes frequently.

### Adding Fields ###
To add your own fields to the Dashboard, do the following:

  1. Create a DashboardPanel instance.
  2. Call addFormField to add FormField instances (including FormAction's). If you want to receive a notice.
  3. Add the DashboardPanel to the DeveloperDashboard by calling add_panel.

### Logging ###
To log to the dashboard, get a DashboardLogWrapper by calling DashboardLog::get_log_wrapper. Logging is done by calling log(). If you have more advanced needs, you can directly access the Zend_Log instance using $logger.

If your log information is expensive to generate, call is_enabled() first to find out if the log stream is enabled.
