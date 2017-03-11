<?php
//   Copyright 2012 John Collins

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

include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Reminders etc";
include 'php/head.php';
?>
<body>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Cron reminders</h1>
<p>
Use the following form to turn on or off the paid/unpaid reminders, the reminders about
games not being played.
</p>
<form action="cronadj2.php" method="post" enctype="application/x-www-form-urlencoded" name='cform'>
<?php
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

EOT;
?>
<p><input type="submit" name="Sub" value="Save changes"> when ready.</p>
</form>
</div>
</div>
</body>
</html>
