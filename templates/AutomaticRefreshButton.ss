<div id="ARB-{$Title}" class="off">
<% if ButtonContent %>$ButtonContent<% else %>$Title<% end_if %>
<% if UseButtonTag %>
	<button $AttributesHTML>
		Off
	</button>
<% else %>
	<input $AttributesHTML>
<% end_if %>
<div class="ssdd-progress-bar-{$Title}">&nbsp;</div>
</div>
AutomaticRefreshButton.ss still contains the old button code - remove when done with update!<br/>
<div id="SSDD-toggle-update" class="off">
      Update <span class="btn">Off</span>
      <div class="ssdd-progress-bar">&nbsp;</div>
    </div>
      
