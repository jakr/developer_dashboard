<% loop $GetLoggedData %>
<div class="request $RequestID">
==== $RequestID ====<br />
<% loop $Children %>
<p class="$StreamID">[$StreamID] $Timestamp $Message</p>
<% end_loop %>
</div>
<% end_loop %>