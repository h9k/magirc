$(document).ready(function() {
	$(".shortDateFormat").each(function (idx, elem) {
		if ($(elem).is(":input")) {
			$(elem).val(moment($(elem).val()).format('L'));
		} else {
			$(elem).text(moment($(elem).text()).format('L'));
		}
	});
	$(".longDateFormat").each(function (idx, elem) {
		if ($(elem).is(":input")) {
			$(elem).val(moment($(elem).val()).format('lll'));
		} else {
			$(elem).text(moment($(elem).text()).format('lll'));
		}
	});
	$.extend($.fn.dataTable.defaults, {
        "processing": false,
		"serverSide": false,
		"jQueryUI": true,
		"autoWidth": false,
		"pagingType": "full_numbers",
		"language": mLang.DataTables,
		"createdRow": function( row, data ) {
			for (var i = 1; i < 4; i++) $("td:eq(" + i + ")", row).text(data[i]);
		}
    });
    $.fn.dataTableExt.sErrMode = 'throw';
	$.fn.dataTableExt.aTypes.unshift(function(data) {
		if (data !== null && typeof(data)=='string') {
			if (data.match(/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[012])\.(19|20|21)\d\d ([01][0-9]|2[0-4])\:([0-5][0-9])\:([0-5][0-9])$/)) {
				return 'date-euro';
			} else if (data.match(/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20|21)\d\d ([01][0-9]|2[0-4])\:([0-5][0-9])\:([0-5][0-9])$/)) {
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
			selected: 4
		},
		tooltip: { valueDecimals: 0, yDecimals: 0, xDateFormat: format_datetime_charts },
		legend: { enabled: false },
		exporting: { enabled: false },
		navigation: { buttonOptions: {} },
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
		credits: { enabled: false }
	});
});

function getTimeElapsed(seconds) {
	var days = Math.floor(seconds / 86400);
	var hours = Math.floor((seconds - (days * 86400 ))/3600);
	var minutes = Math.floor((seconds - (days * 86400 ) - (hours *3600 ))/60);
	return days + " " + mLang.Days + " " + hours + " " + mLang.Hours + " " + minutes + " " + mLang.Minutes;
}

function escapeTags(str) {
	return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function getCountryFlag(user) {
    if (user['country_code'] != null && user['country_code'] != '' && user['country_code'] != '??' && user['country_code'] != 'local') {
        var country = user.country ? user.country : user.country_code;
        var title = (user.city) ? (user.city + ', ' + user.region + ', ' + country) : country;
        return '<span class="flag-icon flag-icon-'+user['country_code'].toLowerCase()+'" alt="'+user['country_code']+'" title="'+title+'"></span>';
    } else {
        return '<span class="flag-icon" alt="Unknown" title="'+mLang.Unknown+'"></span>';
    }
}
