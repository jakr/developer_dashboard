var SSDD_refreshRate = 5000;
var updateIntervalId = null;

/**
 * Run an AJAX request to get new log messages from the server.
 */
function developerDashboardGetNewData() {
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
		+ '/getlog/' + newestLogEntry;
	//flash the off/on button to indicate that it is active.
	jQuery('#SSDD-toggle-update').children().first().css('opacity', 0.5)
		.animate({opacity: 1}, 1000);
	jQuery.get(url, function (data) {
		var jqData = jQuery(data);
		//Hide new messages that belong to a stream that has been hidden.
		jqData.children('p').each(function() {
			if(!jQuery('#set-stream-visibility-'+this.className).children().first().hasClass('btn-success')){
				jQuery(this).addClass('hide');
			}
		});
		jQuery(".SSDD-log-area").append(jqData);
		//The animation runs a little faster
		// to make sure the animation is finished before the next refresh.
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').animate({width: '4em'}, 10)
			.animate({width: '0'}, SSDD_refreshRate*0.95);
	});
}

function hideStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).addClass('hide');
}

function showStream(streamID){
	jQuery('.SSDD-log-area .' + streamID).removeClass('hide');
}

//click on the "toggle update" button, enables or disables updates via AJAX.
jQuery(function(){jQuery('#SSDD-toggle-update').toggle(
	function() {
		updateIntervalId = window.setInterval(
				"developerDashboardGetNewData()", SSDD_refreshRate);
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').animate({width: '4em'}, 10)
			.animate({width: '0'}, SSDD_refreshRate);
		jQuery(this).removeClass('off').children('.btn').addClass('btn-success').text('On');
	},
	function() {
		window.clearInterval(updateIntervalId);
		updateIntervalId = null
		console.log(jQuery(this).children('.btn'));
		jQuery('#SSDD-toggle-update .ssdd-progress-bar').stop().css('width', '4em');
		jQuery(this).addClass('off').children('.btn').removeClass('btn-success').text('Off');
	}
)});
//turn on updates
//TODO Enable in final version. Disabled for tests.
//jQuery(function(){jQuery('#SSDD-toggle-update').click();});

//tabs using bootstrap
jQuery(function() {
	if(jQuery('#SSDD-tabs').length == 0) { return; }
	jQuery('#SSDD-tabs a').click(function (e) {
		e.preventDefault();
		jQuery(this).tab('show');
	});
});

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