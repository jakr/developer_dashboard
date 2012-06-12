<!doctype html>
<html>
<head>
<title>SilverStripe Developer Dashboard</title>
</head>
<body class="cms">
<script type="text/javascript">
</script>
<% if $HasMultipleTabs %>
<div id="SSDD-tabs">
  <ul>
    <li><a href="#SSDD-tabs-log">Logs</a></li>
    <% loop Tabs %>
    <li><a href="#SSDD-tabs-$ID">$Title</a></li>
    <% end_loop %>
  </ul>
<% end_if %>
<div id="SSDD-tabs-log">
  <div id="SSDD-toggle-update" class="off">
	Update <span class="ss-ui-button">off</span>
	<div class="progress">&nbsp;</div>
  </div>
  <% loop $GetStreams %>
    <span class="toggle-stream-visibility on" id="toggle-stream-visibility-$StreamID">$StreamID</span>
  <% end_loop %>
  <div id="SSDD-log-area">
    <% include DeveloperDashboardLogCore %>
  </div>
</div>
<% if $HasMultipleTabs %>
  <% loop $Tabs %>
    <div id="SSDD-tabs-$ID">$Content</div>
  <% end_loop %>
</div>
<% end_if %>

</body>
</html>