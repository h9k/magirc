<?php
// $Id$

define('VERSION_MAJOR', '0');
define('VERSION_MINOR', '0');
define('VERSION_REVISION', '0');
define('VERSION_BUILD', '10');
define('VERSION_EXTRA', '-DEV');

if (DEBUG) {
	define('VERSION_FULL', sprintf('%s.%s.%s.%s%s-DEBUG', VERSION_MAJOR, VERSION_MINOR, VERSION_REVISION, VERSION_BUILD, VERSION_EXTRA));
} else {
	define('VERSION_FULL', sprintf('%s.%s.%s.%s%s', VERSION_MAJOR, VERSION_MINOR, VERSION_REVISION, VERSION_BUILD, VERSION_EXTRA));
}

?>