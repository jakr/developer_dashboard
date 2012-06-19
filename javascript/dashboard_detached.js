var SSDD_refreshRate = 5000;
var updateIntervalId = null;

/**
 * Run an AJAX request to get new log messages from the server.
 */
function developerDashboardGetNewData() {
	var newestLogEntry = 0;
	var lastEntry = jQuery("#SSDD-log-area .request").last().get(0);
	if(typeof lastEntry != 'undefined') {
		newestLogEntry = lastEntry.className.split(' ')[1];
	}
	jQuery("#SSDD-log-area .request").each(function() {
		var requestId = this.className.split(' ')[1];
		if(requestId > newestLogEntry){
			newestLogEntry = requestId; 
		}
	});
	var url = window.location.pathname + 'getlog/' + newestLogEntry;
	//flash the off/on button to indicate that it is active.
	jQuery('#SSDD-toggle-update').children().first().css('opacity', 0.5)
		.animate({opacity: 1}, 1000);
	jQuery.get(url, function (data) {
		var jqData = jQuery(data);
		//Hide new messages that belong to a stream that has been hidden.
		jqData.children('p').each(function() {
			if(!jQuery('#toggle-stream-visibility-'+this.className).hasClass('on')){
				jQuery(this).addClass('hide');
			}
		});
		jQuery("#SSDD-log-area").append(jqData);
		//The animation runs a little faster
		// to make sure the animation is finished before the next refresh.
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').animate({width: '4em'}, 10)
			.animate({width: '0'}, SSDD_refreshRate*0.95);
	});
}

function hideStream(streamID){
	jQuery('#SSDD-log-area .' + streamID).addClass('hide');
}

function showStream(streamID){
	jQuery('#SSDD-log-area .' + streamID).removeClass('hide');
}

//click on the "toggle update" button, enables or disables updates via AJAX .
jQuery(function(){jQuery('#SSDD-toggle-update').toggle(
	function() {
		updateIntervalId = window.setInterval(
				"developerDashboardGetNewData()", SSDD_refreshRate);
		jQuery(this).removeClass().addClass('on').children().first().
			text('on').addClass('ss-ui-action-constructive');
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').animate({width: '4em'}, 10)
			.animate({width: '0'}, SSDD_refreshRate);
	},
	function() {
		window.clearInterval(updateIntervalId);
		updateIntervalId = null
		jQuery(this).removeClass().addClass('off').children().first()
			.text('off').removeClass('ss-ui-action-constructive');
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').stop().css('width', '4em');
	}
)});
//turn on updates
jQuery(function(){jQuery('#SSDD-toggle-update').click();});

//display or hide a log stream
jQuery(function() {
	jQuery('.toggle-stream-visibility').click(function(){
		jQuery('#SSDD-log-area .' + this.innerHTML).toggleClass('hide');
		jQuery(this).toggleClass('on');
	})
});

//tabs using bootstrap
jQuery(function() {
	if(jQuery('#SSDD-tabs').length == 0) { return; }
	jQuery('#SSDD-tabs a').click(function (e) {
		e.preventDefault();
		jQuery(this).tab('show');
	});
});
jQuery(function(){
	//wire up enable and hide buttons.
	//jQuery('.btn-group.toggle-stream-visibility')
	// 	.dropdown-menu li
	//});
});