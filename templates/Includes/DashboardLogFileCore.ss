<% loop $ReadLogData %>
<span class="{$LogFileName}-posEOF hide">$posEOF</span>
<% loop $Children %>
<% if Line %><div class="line">$Line</div><% end_if %>
<% end_loop %>
<% end_loop %>