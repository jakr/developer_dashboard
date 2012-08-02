<% loop $GetLoggedData %>
<div class="request $RequestID">
==== $RequestID $RequestMethod $RequestURI (User: $UserID) ====<br />
<% loop $Children %>
<p class="$StreamID">
  <span class="streamID">[$StreamID]</span> 
  <span class="Timestamp">$Timestamp</span> 
  <span class="Message">$Message</span>
</p>
<% end_loop %>
</div>
<% end_loop %>