<?php
//   Copyright 2011-2021 John Collins

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
include 'php/rank.php';
include 'php/player.php';
include 'php/club.php';

$Connection = opendatabase(true);
lg_html_header("Get outstanding matches for player");
lg_html_nav();
print <<<EOT
<h1>Get outstanding matches for player</h1>
<p>Please select the player concerned from the following list.</p>
<p>If name is not a link, player has not got a userid</p>
<table class="plupd">

EOT;

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
			try {
				$pl->fetchdets();
			}
			catch (PlayerException $e) {
				;
			}
			$dname = $pl->display_name(false);
			$url = $pl->userid_url();
			if (strlen($url) != 0)
				print "<a href=\"osmatches.php?asuser=$url\">$dname</a>";
			else
				print $dname;
		}
		print "</td>";
	}
	print "</tr>\n";
}
print "</table>\n";
lg_html_footer();
?>
