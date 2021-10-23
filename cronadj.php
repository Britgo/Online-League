<?php
//   Copyright 2012-2021 John Collins

// *********************************************************************
// Please do not edit the live file directly as it will break the "Git"
// mechanism to update the live files automatically when a new version
// is pushed. Thanks!
// *********************************************************************


//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';

$Connection = opendatabase(true);

lg_html_header("Reminders etc");
lg_html_nav();
print <<<EOT
<h1>Cron reminders</h1>
<p>Use the following form to turn on or off the paid/unpaid reminders, the reminders about
games not being played.</p>
<form action="cronadj2.php" method="post" enctype="application/x-www-form-urlencoded" name='cform'>

EOT;
$nomatchck = "";
$nopaychck = "";
$norsschck = "";
if (is_file("nomatchreminder"))
	$nomatchck = " checked";
if (is_file("nopayreminder"))
	$nopaychck = " checked";
if (is_file("norssrun"))
	$norsschck = " checked";
print <<<EOT
<p><input type="checkbox" name="nomatchrem"$nomatchck />Set to turn off match reminder script.</p>
<p><input type="checkbox" name="nopay"$nopaychck />Set to turn off pay notifications script.</p>
<p><input type="checkbox" name="norss"$norsschck />Set to turn off RSS feed generation.</p>
<p><input type="submit" name="Sub" value="Save changes"> when ready.</p>
</form>

EOT;
lg_html_footer();
?>
