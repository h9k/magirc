// $Id$

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

jQuery.fn.dataTableExt.oSort['de_date-asc']  = function(a,b) {
	var ukDatea = a.split('.');
	var ukDateb = b.split('.');
	
	var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
	var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
	
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['de_date-desc'] = function(a,b) {
	var ukDatea = a.split('.');
	var ukDateb = b.split('.');
	
	var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
	var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
	
	return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

jQuery.fn.dataTableExt.oSort['natural-asc']  = function(a,b) {
	return naturalSort(a,b);
};

jQuery.fn.dataTableExt.oSort['natural-desc'] = function(a,b) {
	return naturalSort(a,b) * -1;
};

/*
 * Natural Sort algorithm for Javascript - Version 0.4 - Released under MIT license
 * Author: Jim Palmer (based on chunking idea from Dave Koelle)
 * Contributors: Mike Grier (mgrier.com), Clint Priest, Kyle Adams
 */
function naturalSort(a, b){
	// setup temp-scope variables for comparison evauluation
	var re = /(^[0-9]+\.?[0-9]*[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,
		sre = /(^[ ]*|[ ]*$)/g,
		hre = /^0x[0-9a-f]+$/i,
		ore = /^0/,
		// convert all to strings and trim()
		x = a.toString().replace(sre, '') || '',
		y = b.toString().replace(sre, '') || '',
		// chunk/tokenize
		xN = x.replace(re, '\0$1\0').split('\0'),
		yN = y.replace(re, '\0$1\0').split('\0'),
		// hex or date detection
		xD = parseInt(x.match(hre)) || (new Date(x)).getTime(),
		yD = parseInt(y.match(hre)) || xD && (new Date(y)).getTime() || null;
	// natural sorting of hex or dates - prevent '1.2.3' valid date
	if ( y.indexOf('.') < 0 && yD )
		if ( xD < yD ) return -1;
		else if ( xD > yD )	return 1;
	// natural sorting through split numeric strings and default strings
	for(var cLoc=0, numS=Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
		// find floats not starting with '0', string or 0 if not defined (Clint Priest)
		oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc]) || xN[cLoc] || 0;
		oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc]) || yN[cLoc] || 0;
		// handle numeric vs string comparison - number < string - (Kyle Adams)
		if (isNaN(oFxNcL) !== isNaN(oFyNcL)) return (isNaN(oFxNcL)) ? 1 : -1; 
		if (oFxNcL < oFyNcL) return -1;
		else if (oFxNcL > oFyNcL) return 1;
	}
	return 0;
}
