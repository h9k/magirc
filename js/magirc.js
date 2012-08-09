$(document).ready(function() {
	$("#loading").ajaxStart(function(){
		$(this).show();
	}).ajaxStop(function(){
		$(this).hide();
	});
	$("#locale").change(function(data) {
		$.cookie("magirc_locale", $("#locale").val(), { expires: 30, path: '/' });
		window.location.reload();
	});
	$.format.date.defaultShortDateFormat = "dd/MM/yyyy";
	$.format.date.defaultLongDateFormat = "dd/MM/yyyy hh:mm:ss";
	$(".shortDateFormat").each(function (idx, elem) {
		if ($(elem).is(":input")) {
			$(elem).val($.format.date($(elem).val(), $.format.date.defaultShortDateFormat));
		} else {
			$(elem).text($.format.date($(elem).text(), $.format.date.defaultShortDateFormat));
		}
	});
	$(".longDateFormat").each(function (idx, elem) {
		if ($(elem).is(":input")) {
			$(elem).val($.format.date($(elem).val(), $.format.date.defaultLongDateFormat));
		} else {
			$(elem).text($.format.date($(elem).text(), $.format.date.defaultLongDateFormat));
		}
	});
	// Datatable default settings
	$.extend($.fn.dataTable.defaults, {
        "bProcessing": true,
		"bServerSide": false,
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"oLanguage": mLang.DataTables
    });
	$.fn.dataTableExt.aTypes.unshift(function(sData) {
		if (sData !== null && typeof(sData)=='string') {
			if (sData.match(/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20|21)\d\d ([01][0-9]|2[0-4])\:([0-5][0-9])\:([0-5][0-9])$/)) {
				return 'date-euro';
			} else if (sData.match(/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20|21)\d\d ([01][0-9]|2[0-4])\:([0-5][0-9])\:([0-5][0-9])$/)) {
				return 'date-uk';
			}
		}
		return null;
	});
	function calcDate(date, dateSplit) {
		var dtDate = date.split(' ');
		var dtTime = dtDate[1].split(':');
		dtDate = dtDate[0].split(dateSplit);
		return (dtDate[2] + dtDate[1] + dtDate[0] + dtTime[0] + dtTime[1] + dtTime[2]) * 1;
	}
	$.fn.dataTableExt.oSort['date-euro-asc'] = function(a, b) {
		var x = calcDate(a, '.');
		var y = calcDate(b, '.');
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	$.fn.dataTableExt.oSort['date-euro-desc'] = function(a, b) {
		var x = calcDate(a, '.');
		var y = calcDate(b, '.');
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	$.fn.dataTableExt.oSort['date-uk-asc'] = function(a, b) {
		var x = calcDate(a, '/');
		var y = calcDate(b, '/');
		return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};
	$.fn.dataTableExt.oSort['date-uk-desc'] = function(a, b) {
		var x = calcDate(a, '/');
		var y = calcDate(b, '/');
		return ((x < y) ? 1 : ((x > y) ? -1 : 0));
	};
	// Highcharts default settings
	Highcharts.setOptions({
		global: { useUTC: false },
		chart: {
			backgroundColor: 'transparent',
			type: 'spline',
			marginRight: 10,
			style: { fontFamily: 'Share, cursive' },
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: { text: null },
		xAxis: {
			type: 'datetime',
			tickPixelInterval: 150,
			ordinal: true
		},
		yAxis: {
			title: { align: 'low' },
			allowDecimals: false,
			plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
		},
		rangeSelector: {
			buttons: [{
				type: 'day',
				count: 1,
				text: mLang.zoom_1d
			},{
				type: 'week',
				count: 1,
				text: mLang.zoom_1w
			},{
				type: 'month',
				count: 1,
				text: mLang.zoom_1m
			}, {
				type: 'month',
				count: 3,
				text: mLang.zoom_3m
			}, {
				type: 'month',
				count: 6,
				text: mLang.zoom_6m
			}, {
				type: 'year',
				count: 1,
				text: mLang.zoom_1y
			}, {
				type: 'all',
				text: mLang.zoom_All
			}],
			selected: 3
		},
		tooltip: { valueDecimals: 0, yDecimals: 0, xDateFormat: format_datetime_charts },
		legend: { enabled: false },
		exporting: { enabled: false },
		plotOptions: {
			spline: {
				lineWidth: 2,
				states: { hover: { lineWidth: 3 } },
				marker: {
					enabled: false,
					states: {
						hover: {
							enabled: true,
							symbol: 'circle',
							radius: 5,
							lineWidth: 1
						}
					}
				}
			},
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'<\/b>: '+ Math.round(this.percentage * 100) / 100 +' %';
					}
				}
			},
			column: {
				dataLabels: {
					enabled: true,
					rotation: -90,
					x: 3,
					y: -12
				}
			}
		},
		rangeSelector: { selected: 4 },
		credits: { enabled: false }
	});
	var menu = $('#chanmenu').menu({
		selected: function(event, ui) {
			$(this).hide();
			var chan = encodeURIComponent(menu.data('channel'));
			switch (ui.item.data('action')) {
				case 'irc':
					location.href = 'irc://'+net_roundrobin+':'+net_port+'/'+chan.replace('%23', '');
					break;
				case 'ircs':
					location.href = 'irc://'+net_roundrobin+':+'+net_port_ssl+'/'+chan.replace('%23', '');
					break;
				case 'webchat':
					location.href = service_webchat + chan;
					break;
				case 'mibbit':
					location.href = 'http://widget.mibbit.com/?settings='+service_mibbit+'&server='+net_roundrobin+'&channel='+chan+'&promptPass=true';
					break;
			}
		}
	}).hide().css({position: 'absolute', zIndex: 1});
	$('.chanbutton').live('click', function(event) {
		menu.data('channel', $(this).parent().parent().attr('id'));
		if (menu.is(':visible') ){
			menu.hide();
			return false;
		}
		menu.menu('deactivate').show();
		menu.position({
			my: "right top",
			at: "right bottom",
			of: this
		});
		$(document).one("click", function() {
			menu.hide();
		});
		return false;
	});
	$('.chanbutton').live({
		mouseenter: function() { $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
		mouseleave: function() { $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
	});
});

function getUserStatus(user) {
	if (user['away']) return '<img src="theme/'+theme+'/img/status/user-away.png" alt="away" title="'+mLang.AwayAs+' '+user['nickname']+'" \/>';
	else if (user['online']) return '<img src="theme/'+theme+'/img/status/user-online.png" alt="online" title="'+mLang.OnlineAs+' '+user['nickname']+'" \/>';
	else return '<img src="theme/'+theme+'/img/status/user-offline.png" alt="offline" title="'+mLang.Offline+'" \/>';
}
function getUserExtra(user) {
	var out = '';
	if (user['bot']) out += ' <img src="theme/'+theme+'/img/status/bot.png" alt="bot" title="'+mLang.Bot+'" \/>';
	if (user['service']) out += ' <img src="theme/'+theme+'/img/status/service.png" alt="service" title="'+mLang.Service+'" \/>';
	if (user['operator']) out += ' <img src="theme/'+theme+'/img/status/operator.png" alt="oper" title="'+user['operator_level']+'" \/>';
	if (user['helper']) out += ' <img src="theme/'+theme+'/img/status/help.png" alt="help" title="'+mLang.Helper+'" \/>';
	return out;
}
function getCountryFlag(user) {
	if (user['country_code'] != null && user['country_code'] != '' && user['country_code'] != '??' && user['country_code'] != 'local') {
		return '<img src="theme/'+theme+'/img/flags/'+user['country_code'].toLowerCase()+'.png" alt="'+user['country_code']+'" title="'+user['country']+'" />';
	} else {
		return '<img src="theme/'+theme+'/img/flags/unknown.png" alt="Unknown" title="'+mLang.Unknown+'" />';
	}
}
function getChannelLinks(chan) {
	if (net_roundrobin || service_webchat) {
		return '<button type="button" title="'+mLang.Join+'..." class="chanbutton ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icons" style="height:18px; width:30px; margin:0; vertical-align:middle;"><span class="ui-button-icon-secondary ui-icon ui-icon-triangle-1-s"></span></button>';
	} else {
		return ''
	}
}
function getTimeElapsed(seconds) {
	var days = Math.floor(seconds / 86400);
	var hours = Math.floor((seconds - (days * 86400 ))/3600)
	var minutes = Math.floor((seconds - (days * 86400 ) - (hours *3600 ))/60)
	return days + " " + mLang.Days + " " + hours + " " + mLang.Hours + " " + minutes + " " + mLang.Minutes;
}