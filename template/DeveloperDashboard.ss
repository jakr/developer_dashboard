<!doctype html>
<html>
<head>
<title>SilverStripe Developer Dashboard</title>
</head>
<body class="cms">
<script type="text/javascript">
</script>
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
</body>
</html>