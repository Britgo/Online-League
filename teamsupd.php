<?php
//   Copyright 2011 John Collins

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
include 'php/team.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Teams";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Update Teams</h1>
<p>Please select the team to be updated from the following list.</p>
<p>To add a new team select the menu entry, or you can click
on one at random below and just change the entries on the form.</p>
<table class="classupdb">
<?php
$teamlist = list_all_teams();
$countteams = count($teamlist);
$rows = floor(($countteams + 3) / 4);
for ($row = 0; $row < $rows; $row++) {
	print "<tr>\n";
	for ($col = 0; $col < 4;  $col++)  {
		$ind = $row + $col * $rows;
		print "<td>";
		if ($ind >= $countteams)
			print "&nbsp;";
		else {
			$tm = $teamlist[$ind];
			print "<a href=\"updindteam.php?{$tm->urlof()}\">{$tm->display_name()}</a>";		
		}
		print "</td>";
	}
	print "</tr>\n";
}
?>
</table>
</div>
</div>
</body>
</html>
