var SSDD_refreshRate = 5000;
var updateIntervalId = null;

/**
 * Run an AJAX request to get new log messages from the server.
 */
function developerDashboardGetNewData(buttonID, refreshRate) {
	var newestLogEntry = 0;
	var lastEntry = jQuery(".SSDD-log-area .request").last().get(0);
	if(typeof lastEntry != 'undefined') {
		newestLogEntry = lastEntry.className.split(' ')[1];
	}
	jQuery(".SSDD-log-area .request").each(function() {
		var requestId = this.className.split(' ')[1];
		if(requestId > newestLogEntry){
			newestLogEntry = requestId; 
		}
	});
	var url = window.location.pathname;
	//append slash (if missing).
	url = url + (url.charAt(url.length - 1) == '/' ? '' : '/' ) 
		+ 'getlog/' + newestLogEntry;
	//flash the off/on button to indicate that it is active.
	jQuery('#ARB-' + buttonID).children().first().css('opacity', 0.5)
		.animate({opacity: 1}, 500);
	jQuery.get(url, function (data) {
		var jqData = jQuery(data);
		//Hide new messages that belong to a stream that has been hidden.
		jqData.children('p').each(function() {
			if(!jQuery('#set-stream-visibility-'+this.className).children().first().hasClass('btn-success')){
				jQuery(this).addClass('hide');
			}
		});
		jQuery(".SSDD-log-area").append(jqData);
	});
	startUpdate(buttonID, refreshRate);
}

function startUpdate(buttonID, refreshRate){
	var button = jQuery('#ARB-' + buttonID);
	if(button.hasClass('off')){
		button.removeClass('off').children('.btn').addClass('btn-success').text('On');
	}
	jQuery('#ssdd-progress-bar-' + buttonID).animate({width: '4em'}, 10)
	.animate({width: '0'}, {
			duration: refreshRate, 
			complete: function(){developerDashboardGetNewData(buttonID, refreshRate);}
		}
	);
}

function stopUpdate(buttonID){
	jQuery('#ssdd-progress-bar-' + buttonID).stop().css('width', '4em');
	jQuery('#ARB-' + buttonID).addClass('off').children('.btn').removeClass('btn-success').text('Off');
	
}

function hideStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).addClass('hide');
}

function showStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).removeClass('hide');
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

//Show / Hide Timestamps
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

//turn on updates
//TODO Enable in final version. Disabled for tests.
//jQuery(function(){jQuery('#SSDD-toggle-update').click();});


//wire up enable and hide stream buttons.
jQuery(function(){
	jQuery('.btn-group.set-stream-visibility').each(function(index){
		var streamId = jQuery(this).children().first().text();
		jQuery(this).children('.dropdown-menu').children().each(function(index){
			var jqt = jQuery(this); 
			if(jqt.hasClass('ssdd-stream-show')){
				jqt.click(function(){ //click on show stream
					showStream(streamId);
					jqt.parent().prev().addClass('btn-success');
				});
			} else if(jqt.hasClass('ssdd-stream-hide')){
				jqt.click(function(){ //click on hide stream
					hideStream(streamId);
					jqt.parent().prev().removeClass('btn-success');
				});
			} else if(jqt.hasClass('ssdd-stream-disable')){
				jqt.click(function(){ //click on disable stream
					alert("Not implemented");
				});
			}
			
		})
	});
});