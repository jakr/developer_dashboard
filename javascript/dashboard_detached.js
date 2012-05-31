var SSDD_refreshRate = 5000;
var DeveloperDashboardLogMessages = new Object({
	"elementToString":function(key){
		var ret = "==== "+key +" ====\n";
		for(var element in this.data[key]){
			var e = this.data[key][element];
			ret += '[' + e.streamID + '] ' + e.time + ' ' + e.message +"\n";
		}
		return ret;
	},
	newestEntry:function(){
		var newestEntry = 0;
		for(var key in this.data){
			if(key > newestEntry){
				newestEntry = key;
			}
		}
		return newestEntry;
	},
	"data":null
});
var updateIntervalId = null;

function developerDashboardGetNewData(){
	var url = window.location.pathname + 'GetLog/' + 
		DeveloperDashboardLogMessages.newestEntry();
	jQuery('#SSDD_ToggleUpdate').children().first().css('opacity', 0.5).animate({opacity: 1}, 1000);
	jQuery.getJSON(url, function (json){
		for(var key in json){
			DeveloperDashboardLogMessages.data[key] = json[key];
			jQuery("#SSDD_LogArea pre").append(
					DeveloperDashboardLogMessages.elementToString(key));
		}
		jQuery('#SSDD_ToggleUpdate .progress').animate({width: '4em'}, 10).animate({
			width: '0'}, SSDD_refreshRate*0.95);
		//to make sure the animation is finished before the next refresh, it runs a little faster.
	});
}
jQuery(function(){jQuery('#SSDD_ToggleUpdate').toggle(
	function() {
		updateIntervalId = window.setInterval(
				"developerDashboardGetNewData()", SSDD_refreshRate);
		jQuery(this).removeClass('off').addClass('on').children().first().text('on');
		jQuery('#SSDD_ToggleUpdate .progress').animate({width: '4em'}, 10).animate({
			width: '0'}, SSDD_refreshRate);
	},
	function() {
		window.clearInterval(updateIntervalId);
		updateIntervalId = null
		jQuery(this).removeClass('on').addClass('off').children().first().text('off');
		jQuery('#SSDD_ToggleUpdate .progress').stop().css('width', '4em');
	}
)});
jQuery(function(){jQuery('#SSDD_ToggleUpdate').click();});