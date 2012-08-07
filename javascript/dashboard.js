var SSDD_refreshRate = 5000;

// Run an AJAX request to get new log messages from the server.
// urlFunc is a function that returns the URL to call,
// complete is a function that returns a jQuery object to which the
//  result of the request should be appended.
function dashboardLogGetNewData(buttonID, refreshRate, urlFunc, completeFunc) {
	var url = window.location.pathname;
	//append slash (if missing).
	url = url + (url.charAt(url.length - 1) == '/' ? '' : '/' );
	url = url + urlFunc();

	//flash the off/on button to indicate that it is active.
	jQuery('#ARB-' + buttonID).children().first().css('opacity', 0.5)
		.animate({opacity: 1}, 500);

	jQuery.get(url, function (data) {
		var jqData = jQuery(data);
		if(jqData.length == 0) return;
		completeFunc(jqData);
	});
	startUpdateCountdown(buttonID, refreshRate, urlFunc, completeFunc);
}

//callback for log panel
function dashboardLogUpdateComplete(jqData){
	var timestampHidden = jQuery('.SSDD-log-area .Timestamp').first().hasClass('hide');
	//streams that have no control button
	var missing = new Object();
	//Hide new messages that belong to a stream that has been hidden.
	jqData.children('div').each(function() {
		var btn = jQuery('#set-stream-visibility-'+this.className +' .btn');
		var exists = btn.length > 0;
		var visible = btn.hasClass('btn-success');
		if(!exists) missing[this.className] = true;
		if(exists && !visible) jQuery(this).addClass('hide');
		if(timestampHidden){
			jQuery(this).children('.Timestamp').addClass('hide');
		}
	});
	var area = jQuery('.SSDD-log-area').append(jqData);
	area.animate({ scrollTop: area.prop('scrollHeight') - area.height() }, 300);
	for(var streamID in missing){
		getNewButton(streamID);
	}
	hideOldRequests();
}

// Hide all requests older than the limit set in the show_last_requests dropdown.
function hideOldRequests(){
	var value = jQuery('#show_last_requests .dropdown').val();
	if(value == 'all'){
		jQuery('.SSDD-log-area .request').removeClass('hide');
		return;
	}
	var target = jQuery('.SSDD-log-area .request');
	target.addClass('hide');
	target.filter(':gt('+(target.length - value - 1)+')').removeClass('hide');
}

function hideOldLines(){
	var value = jQuery('#show_last_lines .dropdown').val();
	var target = jQuery('#Root_Files .CompositeField .line');
	if(value == '100+' || target.length - value <= 0){
		target.removeClass('hide');
		return;
	}
	target.addClass('hide');
	target.filter(':gt('+(target.length - value - 1)+')').removeClass('hide');
	
}

// Get the html for the button that controls streamID using an AJAX request.
function getNewButton(streamID){
	var url = window.location.pathname;
	//append slash (if missing).
	url = url + (url.charAt(url.length - 1) == '/' ? '' : '/' ) 
		+ 'getstreambutton/' + streamID;
	jQuery.get(url, function(data){
		jQuery('.SSDD-log-stream-visibility-buttons').append(data);
	});
}

// Start automatic update countdown for the button with id buttonID
// urlFunc is a function that returns the URL to call,
// completeFunc is a function that is called with the result of the request.
function startUpdateCountdown(buttonID, refreshRate, urlFunc, completeFunc){
	var button = jQuery('#ARB-' + buttonID);
	if(button.hasClass('off')){
		button.removeClass('off').children('.btn').addClass('btn-success').text('On');
	}
	jQuery('#ARB-progress-bar-' + buttonID).animate({width: '4em'}, 10)
	.animate({width: '0'}, {
			duration: refreshRate, 
			complete: function(){
				dashboardLogGetNewData(buttonID, refreshRate, urlFunc, completeFunc);
			}
		}
	);
}

// Stop automatic update for the button with id buttonID
function stopUpdateCountdown(buttonID){
	jQuery('#ARB-progress-bar-' + buttonID).stop().css('width', '4em');
	jQuery('#ARB-' + buttonID).addClass('off').children('.btn')
		.removeClass('btn-success').text('Off');
}

// Hide the stream streamID
function hideStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).addClass('hide');
	var buttonSelector = '#set-stream-visibility-' + streamID + ' .btn'; 
	jQuery(buttonSelector).removeClass('btn-success');
}

// Show the stream streamID
function showStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).removeClass('hide');
	var buttonSelector = '#set-stream-visibility-' + streamID + ' .btn'; 
	jQuery(buttonSelector).addClass('btn-success');
}

// Hide all streams except the one specified by showStreamID.
function hideOtherStreams(showStreamID){
	jQuery('.set-stream-visibility').each(function(){
		hideStream(jQuery(this).children().first().text());
	})
	showStream(showStreamID);
}

//click on the "toggle update" button, enables or disables updates via AJAX.
jQuery(function(){
	jQuery('#ARB-action_getlog').toggle(
		function() {
			startUpdateCountdown('action_getlog', 5000, 
				function(){
					var lastEntry = jQuery(".SSDD-log-area .request").last().get(0);
					var	newestLogEntry = 0;
					if(typeof lastEntry != 'undefined') {
						newestLogEntry = lastEntry.className.split(' ')[1];
					}
					return 'getlog/' + newestLogEntry;
				},
				dashboardLogUpdateComplete
			);
		},
		function() {
			stopUpdateCountdown('action_getlog');
		}
	);
	jQuery('#ARB-action_readlogfile').toggle(
		function() {
			startUpdateCountdown('action_readlogfile', 5000,
				function(){
					var filename = jQuery('#Root_Files .SelectionGroup :checked').val();
					var offset = jQuery('#Root_Files .SelectionGroup .'+filename+'-posEOF').last().html();
					return 'readlogfile/' + filename + '/' + offset ;
				},
				function(jqData){
					var filename = jQuery('#Root_Files .SelectionGroup :checked').val();
					if(jqData.filter('.line').length == 0) return;
					jQuery('#Root_Files .SSDD-log-file-area-'+filename).append(jqData);
					hideOldLines();
				}
			);
		},
		function() {
			stopUpdateCountdown('action_readlogfile');
		}
	);
});

//Show / Hide Timestamps. Could be done using toggleClass,
// but this way it will fix elements that have the wrong state. 
jQuery(function(){
	jQuery('#SSDD-log-display_timestamps').change(
		function(event) {
			if(event.target.checked){
				jQuery('.Timestamp').removeClass('hide');
			} else {
				jQuery('.Timestamp').addClass('hide');
			}
		}
	);
	jQuery('#SSDD-log-toggle_display_timestamp').click();
});

//Uncomment next line to turn on automatic updating on first load.
//jQuery(function(){jQuery('#SSDD-toggle-update').click();});

//wire up enable and hide stream buttons.
jQuery(function(){
	jQuery('.btn-group.set-stream-visibility').each(function(index){
		var streamId = jQuery(this).children().first().text();
		var menu = jQuery(this).children('.dropdown-menu');
		
		menu.children('.ssdd-stream-show').click(function(){
			showStream(streamId);
		});
		menu.children('.ssdd-stream-hide').click(function(){
			hideStream(streamId);
		});
		menu.children('.ssdd-stream-hide-others').click(function(){
			hideOtherStreams(streamId);
		});
	});
});

//onchange event for the dropdown that controls the number of requests displayed.
jQuery(function(){
	jQuery('#show_last_requests .dropdown').change(hideOldRequests);
	jQuery('#show_last_lines .dropdown').change(hideOldLines);
});

//check that all required buttons are present and load missing ones.
jQuery(function(){
	var missing = new Object();
	jQuery('.SSDD-log-area .request div').each(function(){
		if(jQuery('#set-stream-visibility-'+this.className).length == 0){
			missing[this.className] = true;
		}
	})
	for(var streamID in missing){
		getNewButton(streamID);
	}
});

jQuery(function(){
	jQuery('#Root_Files .SelectionGroup input').change(function(event){
		if(!event.target.checked) return;
		jQuery('#Root_Files .SelectionGroup .CompositeField').addClass('hide');
		jQuery('#Root_Files .SSDD-log-file-area-'+event.target.value).removeClass('hide');
	});
	jQuery('#Root_Files .SelectionGroup input').first().click();
	//.first().attr('checked', 'checked')
});