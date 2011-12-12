// $Id$

jQuery.fn.dataTableExt.oSort['anti-the-asc']  = function(a,b) {
    var x = a.replace(/^the /i, "");
    var y = b.replace(/^the /i, "");
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['anti-the-desc'] = function(a,b) {
    var x = a.replace(/^the /i, "");
    var y = b.replace(/^the /i, "");
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a,b) {
    var x = (a == "-") ? 0 : a.replace( /,/, "." );
    var y = (b == "-") ? 0 : b.replace( /,/, "." );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a,b) {
    var x = (a == "-") ? 0 : a.replace( /,/, "." );
    var y = (b == "-") ? 0 : b.replace( /,/, "." );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['currency-asc'] = function(a,b) {
    /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
    var x = a == "-" ? 0 : a.replace( /,/g, "" );
    var y = b == "-" ? 0 : b.replace( /,/g, "" );
     
    /* Remove the currency sign */
    x = x.substring( 1 );
    y = y.substring( 1 );
     
    /* Parse and return */
    x = parseFloat( x );
    y = parseFloat( y );
    return x - y;
};
 
jQuery.fn.dataTableExt.oSort['currency-desc'] = function(a,b) {
    /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
    var x = a == "-" ? 0 : a.replace( /,/g, "" );
    var y = b == "-" ? 0 : b.replace( /,/g, "" );
     
    /* Remove the currency sign */
    x = x.substring( 1 );
    y = y.substring( 1 );
     
    /* Parse and return */
    x = parseFloat( x );
    y = parseFloat( y );
    return y - x;
};

function trim(str) {
    str = str.replace(/^\s+/, '');
    for (var i = str.length - 1; i >= 0; i--) {
        if (/\S/.test(str.charAt(i))) {
            str = str.substring(0, i + 1);
            break;
        }
    }
    return str;
}
 
jQuery.fn.dataTableExt.oSort['date-euro-asc'] = function(a, b) {
    if (trim(a) != '') {
        var frDatea = trim(a).split(' ');
        var frTimea = frDatea[1].split(':');
        var frDatea2 = frDatea[0].split('/');
        var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
    } else {
        var x = 10000000000000; // = l'an 1000 ...
    }
 
    if (trim(b) != '') {
        var frDateb = trim(b).split(' ');
        var frTimeb = frDateb[1].split(':');
        frDateb = frDateb[0].split('/');
        var y = (frDateb[2] + frDateb[1] + frDateb[0] + frTimeb[0] + frTimeb[1] + frTimeb[2]) * 1;                     
    } else {
        var y = 10000000000000;                    
    }
    var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
    return z;
};
 
jQuery.fn.dataTableExt.oSort['date-euro-desc'] = function(a, b) {
    if (trim(a) != '') {
        var frDatea = trim(a).split(' ');
        var frTimea = frDatea[1].split(':');
        var frDatea2 = frDatea[0].split('/');
        var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;                      
    } else {
        var x = 10000000000000;                    
    }
 
    if (trim(b) != '') {
        var frDateb = trim(b).split(' ');
        var frTimeb = frDateb[1].split(':');
        frDateb = frDateb[0].split('/');
        var y = (frDateb[2] + frDateb[1] + frDateb[0] + frTimeb[0] + frTimeb[1] + frTimeb[2]) * 1;                     
    } else {
        var y = 10000000000000;                    
    }                  
    var z = ((x < y) ? 1 : ((x > y) ? -1 : 0));                  
    return z;
};

jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('/');
    var ukDateb = b.split('/');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

function calculate_date(date) {
    var date = date.replace(" ", "");
     
    if (date.indexOf('.') > 0) {
        /*date a, format dd.mn.(yyyy) ; (year is optional)*/
        var eu_date = date.split('.');
    } else {
        /*date a, format dd/mn/(yyyy) ; (year is optional)*/
        var eu_date = date.split('/');
    }
     
    /*year (optional)*/
    if (eu_date[2]) {
        var year = eu_date[2];
    } else {
        var year = 0;
    }
     
    /*month*/
    var month = eu_date[1];
    if (month.length == 1) {
        month = 0+month;
    }
     
    /*day*/
    var day = eu_date[0];
    if (day.length == 1) {
        day = 0+day;
    }
     
    return (year + month + day) * 1;
}
 
jQuery.fn.dataTableExt.oSort['eu_date-asc'] = function(a, b) {
    x = calculate_date(a);
    y = calculate_date(b);
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['eu_date-desc'] = function(a, b) {
    x = calculate_date(a);
    y = calculate_date(b);
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

jQuery.fn.dataTableExt.oSort['file-size-asc']  = function(a,b) {
    var x = a.substring(0,a.length - 2);
    var y = b.substring(0,b.length - 2);
        
    var x_unit = (a.substring(a.length - 2, a.length) == "MB" ? 1000 : (a.substring(a.length - 2, a.length) == "GB" ? 1000000 : 1));
    var y_unit = (b.substring(b.length - 2, b.length) == "MB" ? 1000 : (b.substring(b.length - 2, b.length) == "GB" ? 1000000 : 1));
     
    x = parseInt( x * x_unit );
    y = parseInt( y * y_unit );
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['file-size-desc'] = function(a,b) {
    var x = a.substring(0,a.length - 2);
    var y = b.substring(0,b.length - 2);
 
    var x_unit = (a.substring(a.length - 2, a.length) == "MB" ? 1000 : (a.substring(a.length - 2, a.length) == "GB" ? 1000000 : 1));
    var y_unit = (b.substring(b.length - 2, b.length) == "MB" ? 1000 : (b.substring(b.length - 2, b.length) == "GB" ? 1000000 : 1));
 
    x = parseInt( x * x_unit);
    y = parseInt( y * y_unit);
 
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['formatted-num-asc'] = function(x,y){
 x = x.replace(/[^\d\-\.\/]/g,'');
 y = y.replace(/[^\d\-\.\/]/g,'');
 if(x.indexOf('/')>=0)x = eval(x);
 if(y.indexOf('/')>=0)y = eval(y);
 return x/1 - y/1;
}
jQuery.fn.dataTableExt.oSort['formatted-num-desc'] = function(x,y){
 x = x.replace(/[^\d\-\.\/]/g,'');
 y = y.replace(/[^\d\-\.\/]/g,'');
 if(x.indexOf('/')>=0)x = eval(x);
 if(y.indexOf('/')>=0)y = eval(y);
 return y/1 - x/1;
}

jQuery.fn.dataTableExt.oSort['title-numeric-asc']  = function(a,b) {
    var x = a.match(/title="*(-?[0-9\.]+)/)[1];
    var y = b.match(/title="*(-?[0-9\.]+)/)[1];
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['title-numeric-desc'] = function(a,b) {
    var x = a.match(/title="*(-?[0-9\.]+)/)[1];
    var y = b.match(/title="*(-?[0-9\.]+)/)[1];
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['title-string-asc']  = function(a,b) {
    var x = a.match(/title="(.*?)"/)[1].toLowerCase();
    var y = b.match(/title="(.*?)"/)[1].toLowerCase();
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['title-string-desc'] = function(a,b) {
    var x = a.match(/title="(.*?)"/)[1].toLowerCase();
    var y = b.match(/title="(.*?)"/)[1].toLowerCase();
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['ip-address-asc']  = function(a,b) {
    var m = a.split("."), x = "";
    var n = b.split("."), y = "";
    for(var i = 0; i < m.length; i++) {
        var item = m[i];
        if(item.length == 1) {
            x += "00" + item;
        } else if(item.length == 2) {
            x += "0" + item;
        } else {
            x += item;
        }
    }
    for(var i = 0; i < n.length; i++) {
        var item = n[i];
        if(item.length == 1) {
            y += "00" + item;
        } else if(item.length == 2) {
            y += "0" + item;
        } else {
            y += item;
        }
    }
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['ip-address-desc']  = function(a,b) {
    var m = a.split("."), x = "";
    var n = b.split("."), y = "";
    for(var i = 0; i < m.length; i++) {
        var item = m[i];
        if(item.length == 1) {
            x += "00" + item;
        } else if (item.length == 2) {
            x += "0" + item;
        } else {
            x += item;
        }
    }
    for(var i = 0; i < n.length; i++) {
        var item = n[i];
        if(item.length == 1) {
            y += "00" + item;
        } else if (item.length == 2) {
            y += "0" + item;
        } else {
            y += item;
        }
    }
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['monthYear-sort-asc']  = function(a,b) {
    a = new Date('01 '+a);
    b = new Date('01 '+b);
    return ((a < b) ? -1 : ((a > b) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['monthYear-sort-desc'] = function(a,b) {
    a = new Date('01 '+a);
    b = new Date('01 '+b);
    return ((a < b) ? 1 : ((a > b) ?  -1 : 0));
};

jQuery.fn.dataTableExt.oSort['natural-asc']  = function(a,b) {
    return naturalSort(a,b);
};
 
jQuery.fn.dataTableExt.oSort['natural-desc'] = function(a,b) {
    return naturalSort(a,b) * -1;
};

/*
 * Natural Sort algorithm for Javascript - Version 0.6 - Released under MIT license
 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
 * Contributors: Mike Grier (mgrier.com), Clint Priest, Kyle Adams, guillermo
 */
function naturalSort (a, b) {
	var re = /(^-?[0-9]+(\.?[0-9]*)[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,
		sre = /(^[ ]*|[ ]*$)/g,
		dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
		hre = /^0x[0-9a-f]+$/i,
		ore = /^0/,
		// convert all to strings and trim()
		x = a.toString().replace(sre, '') || '',
		y = b.toString().replace(sre, '') || '',
		// chunk/tokenize
		xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
		yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
		// numeric, hex or date detection
		xD = parseInt(x.match(hre)) || (xN.length != 1 && x.match(dre) && Date.parse(x)),
		yD = parseInt(y.match(hre)) || xD && y.match(dre) && Date.parse(y) || null;
	// first try and sort Hex codes or Dates
	if (yD)
		if ( xD < yD ) return -1;
		else if ( xD > yD )	return 1;
	// natural sorting through split numeric strings and default strings
	for(var cLoc=0, numS=Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
		// find floats not starting with '0', string or 0 if not defined (Clint Priest)
		oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc]) || xN[cLoc] || 0;
		oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc]) || yN[cLoc] || 0;
		// handle numeric vs string comparison - number < string - (Kyle Adams)
		if (isNaN(oFxNcL) !== isNaN(oFyNcL)) return (isNaN(oFxNcL)) ? 1 : -1; 
		// rely on string comparison if different types - i.e. '02' < 2 != '02' < '2'
		else if (typeof oFxNcL !== typeof oFyNcL) {
			oFxNcL += ''; 
			oFyNcL += ''; 
		}
		if (oFxNcL < oFyNcL) return -1;
		if (oFxNcL > oFyNcL) return 1;
	}
	return 0;
}

jQuery.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
    var x = a.replace( /<.*?>/g, "" );
    var y = b.replace( /<.*?>/g, "" );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
    var x = a.replace( /<.*?>/g, "" );
    var y = b.replace( /<.*?>/g, "" );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['percent-asc']  = function(a,b) {
    var x = (a == "-") ? 0 : a.replace( /%/, "" );
    var y = (b == "-") ? 0 : b.replace( /%/, "" );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['percent-desc'] = function(a,b) {
    var x = (a == "-") ? 0 : a.replace( /%/, "" );
    var y = (b == "-") ? 0 : b.replace( /%/, "" );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

function fnPriority( a )
{
    if ( a == "High" )        { return 1; }
    else if ( a == "Medium" ) { return 2; }
    else if ( a == "Low" )    { return 3; }
    return 4;
}
 
jQuery.fn.dataTableExt.oSort['priority-asc']  = function(a,b) {
    var x = fnPriority( a );
    var y = fnPriority( b );
     
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['priority-desc'] = function(a,b) {
    var x = fnPriority( a );
    var y = fnPriority( b );
     
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['signed-num-asc'] = function(x,y){
    x = (x=="-" || x==="") ? 0 : x.replace('+','')*1;
    y = (y=="-" || y==="") ? 0 : y.replace('+','')*1;
    return x - y;
}
jQuery.fn.dataTableExt.oSort['signed-num-desc'] = function(x,y){
    x = (x=="-" || x==="") ? 0 : x.replace('+','')*1;
    y = (y=="-" || y==="") ? 0 : y.replace('+','')*1;
    return y - x;
}

var weekdays = new Array();
weekdays['monday'] = 1;
weekdays['tuesday'] = 2;
weekdays['wednesday'] = 3;
weekdays['thursday'] = 4;
weekdays['friday'] = 5;
weekdays['saturday'] = 6;
weekdays['sunday'] = 7;
 
jQuery.fn.dataTableExt.oSort['weekdays-sort-asc']  = function(a,b) {
    a = a.toLowerCase();
    b = b.toLowerCase();
    return ((weekdays[a] < weekdays[b]) ? -1 : ((weekdays[a] > weekdays[b]) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['weekdays-sort-desc'] = function(a,b) {
    a = a.toLowerCase();
    b = b.toLowerCase();
    return ((weekdays[a] < weekdays[b]) ? 1 : ((weekdays[a] > weekdays[b]) ?  -1 : 0));
};
