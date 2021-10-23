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
include 'php/matchdate.php';
include 'php/params.php';
include 'php/season.php';

$Connection = opendatabase();
lg_html_header("Historical League Tables");
lg_html_nav();
print <<<EOT
<h1>Historical League Tables</h1>
<h2>Team League</h2>

EOT;
$seasons = list_seasons();
if (count($seasons) == 0) {
	print <<<EOT
<p>There are currently no past seasons to display.
Please come back soon!
</p><p>Please <a href="javascript:history.back()">click here</a> to go back.</p>

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

print <<<EOT
<h2>Individual League</h2>
<p>This is the former Individual League, which ran until October 2012.</p>

EOT;

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
lg_html_footer();
?>
