<div id="ARB-{$Title}" class="automatic-refresh-button off">
<% if ButtonContent %>$ButtonContent<% else %>$Title<% end_if %>
<div $AttributesHTML>Off</div>
<noscript>
<% if UseButtonTag %>
	<button $AttributesHTML>
		Off
	</button>
<% else %>
	<input $AttributesHTML>
<% end_if %>
</noscript>
<div class="ssdd-progress-bar" id="ssdd-progress-bar-{$Title}">&nbsp;</div>
</div>
