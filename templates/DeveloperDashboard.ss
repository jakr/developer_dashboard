<!doctype html>
<html>
<head>
<title>SilverStripe Developer Dashboard</title>
</head>
<body class="cms">
$DashboardForm
<% if $HasMultipleTabs %>
<div id="SSDD-tabs">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#SSDD-tabs-log">Logs</a></li>
    <% loop Tabs %>
    <li><a href="#SSDD-tabs-$ID" data-toggle="tab">$Title</a></li>
    <% end_loop %>
  </ul>
<% end_if %>
<div class="tab-content">
  <div class="tab-pane active" id="SSDD-tabs-log">
    <br style="clear: left;"/>
    <div id="SSDD-log-area">
      <% include DeveloperDashboardLogCore %>
    </div>
  </div>
  <% if $HasMultipleTabs %>
    <% loop $Tabs %>
      <div class="tab-pane" id="SSDD-tabs-$ID">$Content</div>
    <% end_loop %>
  <% end_if %>
</div>

</body>
</html>