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
  <ul class="nav nav-tabs">
    <li class="active"><a href="#SSDD-tabs-log">Logs</a></li>
    <% loop Tabs %>
    <li><a href="#SSDD-tabs-$ID" data-toggle="tab">$Title</a></li>
    <% end_loop %>
  </ul>
<% end_if %>
<div class="tab-content">
  <div class="tab-pane active" id="SSDD-tabs-log">
    <div id="SSDD-toggle-update" class="off">
      Update <span class="btn">Off</span>
      <div class="ssdd-progress-bar">&nbsp;</div>
    </div>
    <div class="btn-toolbar">
    <% loop $GetStreams %>
      <div class="btn-group set-stream-visibility" id="set-stream-visibility-$StreamID">
        <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">$StreamID
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li class="ssdd-stream-show">Show</li>
          <li class="ssdd-stream-hide">Hide</li>
          <li class="ssdd-stream-disable">Disable</li>
        </ul>
      </div>
    <% end_loop %>
    </div>
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