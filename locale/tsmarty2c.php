<?php
/**
 * tsmarty2c.php - rips gettext strings from smarty template
 *
 * ------------------------------------------------------------------------- *
 * This library is free software; you can redistribute it and/or             *
 * modify it under the terms of the GNU Lesser General Public                *
 * License as published by the Free Software Foundation; either              *
 * version 2.1 of the License, or (at your option) any later version.        *
 *                                                                           *
 * This library is distributed in the hope that it will be useful,           *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU         *
 * Lesser General Public License for more details.                           *
 *                                                                           *
 * You should have received a copy of the GNU Lesser General Public          *
 * License along with this library; if not, write to the Free Software       *
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA *
 * ------------------------------------------------------------------------- *
 *
 * @package	smarty-gettext
 * @link	http://smarty-gettext.sf.net/
 * @author	Sagi Bashari <sagi@boom.org.il>
 * @copyright 2004-2005 Sagi Bashari
 * Adapted to MagIRC
 */

date_default_timezone_set('UTC');

// smarty open tag
$ldq = preg_quote('{');

// smarty close tag
$rdq = preg_quote('}');

// smarty command
$cmd = preg_quote('t');

// extensions of smarty files, used when going through a directory
$extensions = array('tpl');

// "fix" string - strip slashes, escape and convert new lines to \n
function fs($str)
{
	$str = stripslashes($str);
	$str = str_replace('"', '\"', $str);
	$str = str_replace("\n", '\n', $str);
	return $str;
}

// rips gettext strings from $file and prints them in C format
function do_file($file)
{
	$content = @file_get_contents($file);
	$output = "";

	if (empty($content)) {
		return;
	}

	global $ldq, $rdq, $cmd;

	preg_match_all(
			"/{$ldq}\s*({$cmd})\s*([^{$rdq}]*){$rdq}([^{$ldq}]*){$ldq}\/\\1{$rdq}/",
			$content,
			$matches
	);
	
	for ($i=0; $i < count($matches[0]); $i++) {
		// TODO: add line number
		$output .= "/* $file */\n"; // credit: Mike van Lammeren 2005-02-14
		
		if (preg_match('/plural\s*=\s*["\']?\s*(.[^\"\']*)\s*["\']?/', $matches[2][$i], $match)) {
			$output .= 'ngettext("'.fs($matches[3][$i]).'","'.fs($match[1]).'",x);'."\n";
		} else {
			$output .= 'gettext("'.fs($matches[3][$i]).'");'."\n";
		}

		$output .= "\n";
	}
	return $output;
}

// go through a directory
function do_dir($dir)
{
	$d = dir($dir);
	$output = "";

	while (false !== ($entry = $d->read())) {
		if ($entry == '.' || $entry == '..') {
			continue;
		}

		$entry = $dir.'/'.$entry;

		if (is_dir($entry)) { // if a directory, go through it
			do_dir($entry);
		} else { // if file, parse only if extension is matched
			$pi = pathinfo($entry);
			
			if (isset($pi['extension']) && in_array($pi['extension'], $GLOBALS['extensions'])) {
				$output .= do_file($entry);
			}
		}
	}

	$d->close();
	return $output;
}

$output = do_dir(__DIR__.'/../theme/default/tpl/');
$writefile = fopen(__DIR__.'/strings.c',"w");
fwrite($writefile, $output);
fclose($writefile);

?>
