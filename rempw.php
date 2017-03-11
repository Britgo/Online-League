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
include 'php/rank.php';
include 'php/player.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Remind password";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Remind player of password</h1>
<p>Please select the player to be reminded from the following list.</p>
<table class="plupd">
<?php
$playerlist = list_players();
$countplayers = count($playerlist);
$rows = floor(($countplayers + 3) / 4);
for ($row = 0; $row < $rows; $row++) {
	print "<tr>\n";
	for ($col = 0; $col < 4;  $col++)  {
		$ind = $row + $col * $rows;
		print "<td>";
		if ($ind >= $countplayers)
			print "&nbsp;";
		else {
			$pl = $playerlist[$ind];
			print "<a href=\"rempw2.php?{$pl->urlof()}\">{$pl->display_name(false)}</a>";		
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
