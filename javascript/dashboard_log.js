var SSDD_refreshRate = 5000;
var updateIntervalId = null;

/**
 * Run an AJAX request to get new log messages from the server.
 */
function dashboardLogGetNewData(buttonID, refreshRate) {
	var timestampHidden = jQuery('.Timestamp').first().hasClass('hide');
	var lastEntry = jQuery(".SSDD-log-area .request").last().get(0);
	var newestLogEntry = 0;
	if(typeof lastEntry != 'undefined') {
		newestLogEntry = lastEntry.className.split(' ')[1];
	}
	var url = window.location.pathname;
	//append slash (if missing).
	url = url + (url.charAt(url.length - 1) == '/' ? '' : '/' ) 
		+ 'getlog/' + newestLogEntry;

	//flash the off/on button to indicate that it is active.
	jQuery('#ARB-' + buttonID).children().first().css('opacity', 0.5)
		.animate({opacity: 1}, 500);

	jQuery.get(url, function (data) {
		var jqData = jQuery(data);
		if(jqData.length == 0) return;
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
	});
	startUpdate(buttonID, refreshRate);
}

function getNewButton(streamID){
	var url = window.location.pathname;
	//append slash (if missing).
	url = url + (url.charAt(url.length - 1) == '/' ? '' : '/' ) 
		+ 'getstreambutton/' + streamID;
	jQuery.get(url, function(data){
		jQuery('.SSDD-log-stream-visibility-buttons').append(data);
	});
}

function startUpdate(buttonID, refreshRate){
	var button = jQuery('#ARB-' + buttonID);
	if(button.hasClass('off')){
		button.removeClass('off').children('.btn').addClass('btn-success').text('On');
	}
	jQuery('#ssdd-progress-bar-' + buttonID).animate({width: '4em'}, 10)
	.animate({width: '0'}, {
			duration: refreshRate, 
			complete: function(){dashboardLogGetNewData(buttonID, refreshRate);}
		}
	);
}

function stopUpdate(buttonID){
	jQuery('#ssdd-progress-bar-' + buttonID).stop().css('width', '4em');
	jQuery('#ARB-' + buttonID).addClass('off').children('.btn')
		.removeClass('btn-success').text('Off');
	
}

function hideStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).addClass('hide');
	var buttonSelector = '#set-stream-visibility-' + streamID + ' .btn'; 
	jQuery(buttonSelector).removeClass('btn-success');
}

function showStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).removeClass('hide');
	var buttonSelector = '#set-stream-visibility-' + streamID + ' .btn'; 
	jQuery(buttonSelector).addClass('btn-success');
}

function hideOtherStreams(showStreamID){
	jQuery('.set-stream-visibility').each(function(){
		hideStream(jQuery(this).children().first().text());
	})
	showStream(showStreamID);
}

//click on the "toggle update" button, enables or disables updates via AJAX.
jQuery(function(){jQuery('#ARB-Update').toggle(
	function() {
		startUpdate('Update', 5000);
	},
	function() {
		stopUpdate('Update');
	}
)});

//Show / Hide Timestamps. Could be done using toggleClass,
// but this way it will fix elements that have the wrong state. 
jQuery(function(){
	jQuery('#toggle_display_timestamp').toggle(
		function() {
			jQuery('.Timestamp').addClass('hide');
		},
		function() {
			jQuery('.Timestamp').removeClass('hide');
		}
	);
	jQuery('#toggle_display_timestamp').click();
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