Logging
===================
## Basic ##
To log to the dashboard, get a DashboardLogWrapper by calling DashboardLog::get_log_wrapper. Logging is done by calling log(). If you have more advanced needs, you can directly access the Zend_Log instance using $logger.

If your log information is expensive to generate, call is_enabled() first to find out if the log stream is enabled.

## Filtering by URLs ##
It is possible to enable logging only on specific URLs. This can be done by attaching a DashboardLogURLFilter to the log stream. There is currently no special API to do this. If you already have a DashboardLogWrapper, just use `$logWrapper->logger->addFilter()`.

The rules are evaluated so that accept takes precedence and is the default - if no rule matches, logging is enabled. To allow logging from only one specific URL, create a rule that denies all URLs and one that accepts the specific URL (see second example below).

## Example: Disabling logging for stream DEFAULT in the backend ##
  $urlFilter = new DashboardLogURLFilter();
  $urlFilter->addDenyURL('admin');
  $logWrapper = DashboadLog::get_log_wrapper('DEFAULT');
  $logWrapper->logger->addFilter($urlFilter);
  
## Example: All streams log only on URLs that match a regular expression ##
Note: The API used in this example might change in the future.

  $urlFilter = new DashboardLogURLFilter();
  $urlFilter->addDenyPattern('#.*#'); //deny all
  $urlFilter->addAcceptPattern('#myexample[0-9].*/some/url#');
  foreach(DashboardLogWriter::get_stream_ids() as $streamID){
    DashboardLogWriter::get_log_writer($streamID)->addFilter($urlFilter);
  }
