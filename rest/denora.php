<?php
# $Id$

include_once('../lib/magirc/version.inc.php');
require_once('../lib/magirc/DB.class.php');
require_once('../lib/magirc/Config.class.php');
require_once('../lib/magirc/denora/Denora.class.php');
require_once('../lib/magirc/Magirc.class.php');
require_once('../lib/restler/restler.php');

require_once 'denora/network.php';
require_once 'denora/clientstats.php';
require_once 'denora/countrystats.php';
require_once 'denora/servers.php';
require_once 'denora/users.php';
require_once 'denora/channels.php';

spl_autoload_register('spl_autoload');
$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
$r->addAPIClass('Network');
$r->addAPIClass('ClientStats');
$r->addAPIClass('CountryStats');
$r->addAPIClass('Servers');
$r->addAPIClass('Users');
$r->addAPIClass('Channels');
$r->handle();

?>
