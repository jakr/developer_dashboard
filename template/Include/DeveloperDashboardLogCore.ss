<% loop $GetLoggedData %>
<div class="request $RequestID">
==== $RequestID ====<br />
<% loop $Children %>
<span class="$StreamID">[$StreamID] $Timestamp $Message</span>
<% end_loop %>
</div>
<% end_loop %>