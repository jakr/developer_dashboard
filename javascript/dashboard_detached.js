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

function getNewData(){
	var url = window.location.href + 'GetLog/' + 
		DeveloperDashboardLogMessages.newestEntry();
	jQuery.getJSON(url, function (json){
		for(var key in json){
			DeveloperDashboardLogMessages.data[key] = json[key];
			jQuery("#DeveloperDashboardLogArea pre").append(
					DeveloperDashboardLogMessages.elementToString(key));
		};
	});
}

jQuery(function(){window.setInterval("getNewData()", 5000);});