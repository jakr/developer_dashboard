## Installation ##
The dashboard works as a separate module. As with any other module, download the code, put it in a subfolder and call /dev/build to rebuild the class manifest.

## Viewing the Toolbar ##
The Toolbar is accessible under yoursite/DeveloperDashboard/

By default (when no custom tabs have been added), it will display two tabs:
  * Tools, showing debug functionality, and
  * Logs, showing log information that was written using DashboardLogWriter.

![A screen shot of the Tools Tab](/jakr/developer_dashboard/docs/img/tab_tools.png "The Tools tab")

![A screen shot of the Logs Tab, showing different streams](/jakr/developer_dashboard/docs/img/tab_logs_refresh.png "The logs tab")

Each stream has a control button. Clicking on the button, it is possible to hide information from a stream:
![The stream control menu](/jakr/developer_dashboard/docs/img/tab_logs_stream_menu.png "The stream control menu")

![A screen shot of the Logs Tab, with one stream hidden](/jakr/developer_dashboard/docs/img/tab_logs_hide_stream_default.png "After hiding the DEFAULT stream, it is no longer shown.")

## Usage ##
Please note that the Dashboard is at an early stage of development and that the API still changes frequently.

### Adding Fields ###
To add your own fields to the Dashboard, do the following:

  1. Create a DashboardPanel instance.
  2. Call addFormField to add FormField instances (including FormAction's). If you want to receive a notice.
  3. Add the DashboardPanel to the DeveloperDashboard by calling add_panel.

#### Action Callbacks ####
If you have a form action and want to be notified when its action is triggered, it is neccessary to provide a callback function to addFormField. The easiest way to do this, is if you call addFormField from within a Controller.
If the controller that you are in has a method of the same name as the FormAction, you only need to call `addFormField(new FormAction('somename',...), $this)`. The Form will automatically find that method. If the controller does not have such a method, you need to provide the method name, by calling `addFormField(new FormAction(...), $this, 'myMethodName')`.

### Using the update content callback ###
In some cases, the information you want to display is not available during initialization. This might happen if you try to access Session or the database. For this case, you can register a callback using `setFormContentCallback($controller)`. Before the form is displayed, `$controller->getPanelContent($panel)` will be executed, allowing to add more fields to the panel using `$panel->addFormField()`. It is also possible to access the existing fields using `$panel->Fields()`.

### Logging ###
To log to the dashboard, get a DashboardLogWrapper by calling DashboardLog::get_log_wrapper. Logging is done by calling log(). If you have more advanced needs, you can directly access the Zend_Log instance using $logger.

If your log information is expensive to generate, call is_enabled() first to find out if the log stream is enabled.
