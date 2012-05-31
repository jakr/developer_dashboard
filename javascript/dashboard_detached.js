var SSDD_refreshRate = 5000;
var updateIntervalId = null;

function developerDashboardGetNewData(){
	var newestLogEntry = 0;
	jQuery("#SSDD-log-area .request").each(function(){
		var requestId = this.className.split(' ')[1];
		if(requestId > newestLogEntry){
			newestLogEntry = requestId; 
		}
	});
	console.log(newestLogEntry);
	var url = window.location.pathname + 'GetLog/' + newestLogEntry;
	jQuery('#SSDD-toggle-update').children().first().css('opacity', 0.5).animate({opacity: 1}, 1000);
	jQuery.get(url, function (data){
		jQuery("#SSDD-log-area").append(data);
		//for(var key in data){
		//	DeveloperDashboardLogMessages.data[key] = json[key];
		//	jQuery("#SSDD-log-area").append(
		//			DeveloperDashboardLogMessages.elementToHTML(key));
		//}
		jQuery('#SSDD-toggle-update .progress').animate({width: '4em'}, 10).animate({
			width: '0'}, SSDD_refreshRate*0.95);
		//to make sure the animation is finished before the next refresh, it runs a little faster.
	});
}
jQuery(function(){jQuery('#SSDD-toggle-update').toggle(
	function() {
		updateIntervalId = window.setInterval(
				"developerDashboardGetNewData()", SSDD_refreshRate);
		jQuery(this).removeClass().addClass('on').children().first().
			text('on').addClass('ss-ui-action-constructive');
		jQuery('#SSDD-toggle-update .progress').animate({width: '4em'}, 10).animate({
			width: '0'}, SSDD_refreshRate);
	},
	function() {
		window.clearInterval(updateIntervalId);
		updateIntervalId = null
		jQuery(this).removeClass().addClass('off').children().first().text('off').
			removeClass('ss-ui-action-constructive');
		jQuery('#SSDD-toggle-update .progress').stop().css('width', '4em');
	}
)});
jQuery(function(){jQuery('#SSDD-toggle-update').click();});