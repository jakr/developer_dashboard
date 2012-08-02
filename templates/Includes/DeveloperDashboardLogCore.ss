<% loop $GetLoggedData %>
<div class="request $RequestID">
==== $RequestID $RequestMethod $RequestURI (User: $UserID) ====<br />
<% loop $Children %>
<div class="$StreamID">
  <span class="streamID">[$StreamID]</span> 
  <span class="Timestamp">$Timestamp</span> 
  <span class="Message">$Message</span>
</div>
<% end_loop %>
</div>
<% end_loop %>