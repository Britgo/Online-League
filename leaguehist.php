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
include 'php/opendatabase.php';
include 'php/matchdate.php';
include 'php/params.php';
include 'php/season.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Historical League Tables";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Historical League Tables</h1>
<h2>Team League</h2>
<?php
$seasons = list_seasons();
if (count($seasons) == 0) {
	print <<<EOT
<p>There are currently no past seasons to display.
Please come back soon!
</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back.
</p>

EOT;
}
else {
	print <<<EOT
<table class="teamsb">
<tr>
	<th>Season Name</th>
	<th>Start Date</th>
	<th>End Date</th>
	<th>League table</th>
	<th>Matches</th>
</tr>

EOT;
	foreach ($seasons as $seas) {
		$seas->fetchdets();
		print <<<EOT
<tr>
	<td>{$seas->display_name()}</td>
	<td>{$seas->display_start()}</td>
	<td>{$seas->display_end()}</td>
	<td><a href="seasleague.php?{$seas->urlof()}">Click</a></td>
	<td><a href="seasmatches.php?{$seas->urlof()}">Click</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
?>
<h2>Individual League</h2>
<p>This is the former Individual League, which ran until October 2012.</p>
<?php
$seasons = list_seasons('I');
if (count($seasons) == 0) {
	print <<<EOT
<p>No previous seasons to display data for.</p>

EOT;
}
else {
		print <<<EOT
<table class="teamsb">
<tr>
	<th>Season Name</th>
	<th>Start Date</th>
	<th>End Date</th>
	<th>League table</th>
</tr>

EOT;
	foreach ($seasons as $seas) {
		$seas->fetchdets();
		print <<<EOT
<tr>
	<td>{$seas->display_name()}</td>
	<td>{$seas->display_start()}</td>
	<td>{$seas->display_end()}</td>
	<td><a href="seasileague.php?{$seas->urlof()}">Click</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
?>
</div>
</div>
</body>
</html>
