<div class="btn-group set-stream-visibility" id="set-stream-visibility-{$Title}">
  <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">$Title
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li class="ssdd-stream-show" onclick="showStream('$Title')">Show</li>
    <li class="ssdd-stream-hide" onclick="hideStream('$Title')">Hide</li>
    <li class="ssdd-stream-hide-others" onclick="hideOtherStreams('$Title')">Hide all other streams</li>
  </ul>
</div>