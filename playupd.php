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

$Connection = opendatabase(true);
lg_html_header("Update Players");
lg_html_nav();
print <<<EOT
<h1>Update Players</h1>
<p>Please select the player to be updated from the following list.</p>
<p>To add a new player either select the "new player" menu entry.</p>

EOT;
$playerlist = list_players();
$countplayers = count($playerlist);
$startseq = 0;
while  ($startseq < $countplayers)  {
	$cinit = $playerlist[$startseq]->get_initial();
	$endseq = $startseq + 1;
	while  ($endseq < $countplayers  &&  $playerlist[$endseq]->get_initial() == $cinit)
		$endseq++;
	$nump = $endseq - $startseq;
	$cols = min(4, ceil($nump/10));
	$rows = ceil($nump/$cols);
	print <<<EOT
<table class="plupd">
<tr><th colspan="$cols">$cinit</th></tr>

EOT;
	for ($row = 0; $row < $rows; $row++) {
		print "<tr>\n";
		for ($col = 0; $col < 4;  $col++)  {
			$ind = $startseq + $row + $col * $rows;
			print "<td>";
			if ($ind >= $endseq)
				print "&nbsp;";
			else {
				$pl = $playerlist[$ind];
				print "<a href=\"updindplayer.php?{$pl->urlof()}\">{$pl->display_name(false)}</a>";
			}
			print "</td>";
		}
		print "</tr>\n";
	}
	print "</table>\n";
	$startseq = $endseq;
}
lg_html_footer();
?>
