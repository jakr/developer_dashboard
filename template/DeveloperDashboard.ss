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
      Update <span class="ss-ui-button">off</span>
      <div class="ssdd-progress-bar">&nbsp;</div>
    </div>
    <% loop $GetStreams %>
      <div class="btn-group toggle-stream-visibility on">
        <button class="btn">$StreamID</button>
        <button class="btn dropdown-toggle" data-toggle="dropdown">
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li>Enable</li>
          <li>Hide</li>
          <li>Disable</li>
        </ul>
      </div>
    <% end_loop %>
    <br style="clear: left;"/>
    <% loop $GetStreams %>
      <span class="toggle-stream-visibility on" id="toggle-stream-visibility-$StreamID">$StreamID</span>
    <% end_loop %>
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