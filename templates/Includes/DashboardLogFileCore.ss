<% loop $ReadLogData %>
<span class="{$LogFileName}-posEOF hide">$posEOF</span>
<% loop $Children %>
<div>$Line</div>
<% end_loop %>
<% end_loop %>