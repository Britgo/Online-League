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
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';

$Connection = opendatabase();
lg_html_header("Players List");
lg_html_nav();
print <<<EOT
<h1>Players</h1>

EOT;

switch ($_GET["by"])  {
default:
	$pclub = 1;
	$order = "last,first,rank desc";
	//$next = "club";
	$initials = list_player_initials();
	$byrank = 0;
	break;
case  "club":
	$pclub = 0;
	$order = "club.name,last,first,rank desc";
	//$next = "rank";
	$initials = list_club_initials();
	$byrank = 0;
	break;
case  "rank":
	$pclub = 1;
	$order = "rank desc,last,first,rank desc";
	//$next = "clubrank";
	$initials = list_player_ranks();
	$byrank = 1;
	break;
case  "clubrank":
	$pclub = 0;
	$order = "club.name,rank desc,last,first";
	//$next = "name";
	$initials = list_club_initials();
	$byrank = 0;	// Did mean that
	break;
}

// Provide for 11 columns if not printing club column otherwise 12

$cs = 12;
$cs += $pclub;
if (count($initials) != 0)  {
	print <<<EOT
<a name="Top"></a>
<table class="plinits"><tr>

EOT;
	if ($byrank) {
		foreach ($initials as $init) {
			$r = new Rank($init);
			print "<td><a href=\"#{$r->anchor()}\">{$r->display()}</a></td>\n";
		}
	}
	else  {
		foreach ($initials as $init) {
			print "<td><a href=\"#$init\">$init</a></td>\n";
		}
	}
	print "</tr></table>\n";
}
// $ref = "<a href=\"players.php?by=$next\">";
print <<<EOT
<table class="pllist">
<tr>
<th colspan="3">&nbsp;</th>
<th colspan="4" align="center">Current</th>
<th>&nbsp;</th>
<th colspan="4" align="center">Total</th>
</tr>
<tr>
<th>Name</th>
<th>Rank</th>
<th>Online</th>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>
<td>&nbsp;</td>
<th>P</th>
<th>W</th>
<th>D</th>
<th>L</th>

EOT;
if ($pclub)
	print "<th>Club</th>\n";
print <<<EOT
</tr>

EOT;

$ret = $Connection->query("SELECT first,last,club.name FROM player,club WHERE player.club=club.code ORDER BY $order");
if ($ret && $ret->num_rows) {
	$lclub = "not set";
	$linit = "-";
	$lrank = new Rank(4000);
	while ($row = $ret->fetch_assoc()) {
		$p = new Player($row["first"], $row["last"]);
		$p->fetchdets();
		$club = $row["name"];
		$nrank = $p->Rank;
		if ($byrank)  {
			if ($lrank->notequals($nrank)) {
				$lrank = $nrank;
				print <<<EOT
<tr>
<th colspan=$cs align="center">
<a name="{$nrank->anchor()}"></a>
<a href="#Top">{$nrank->display()}</a>
</th>
</tr>

EOT;
			}
		}
		elseif  ($pclub)  {
			$pinit = $p->get_initial();
			if  ($linit != $pinit)  {
				$linit = $pinit;
				print <<<EOT
<tr>
<th colspan=$cs align="center">
<a name="$pinit"></a>
<a href="#Top">$pinit</a>
</th>
</tr>

EOT;
			}
		}
		else  {
			if  ($lclub != $club)  {
				$cinit = strtoupper(substr($club, 0, 1));
				$ref = "";
				if ($linit != $cinit) {
					$linit = $cinit;
					$ref = "<a name=\"$cinit\"></a>";
				}
				print "<tr><th colspan=\"$cs\" align=\"center\">" . $ref . "<a href=\"#Top\">" . htmlspecialchars($club) . "</a></th></tr>\n";
				$lclub = $club;
			}
		}
		print <<<EOT
<tr>
<td>{$p->display_name()}</td>
<td>{$p->display_rank()}</td>
<td>{$p->display_online()}</td>
<td>{$p->played_games(true)}</td>
<td>{$p->won_games(true)}</td>
<td>{$p->drawn_games(true)}</td>
<td>{$p->lost_games(true)}</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>{$p->played_games()}</td>
<td>{$p->won_games()}</td>
<td>{$p->drawn_games()}</td>
<td>{$p->lost_games()}</td>

EOT;
		if ($pclub)
			print "<td>" . htmlspecialchars($club) . "</td>\n";
		print "</tr>\n";
	}
}
print <<<EOT
</table>
<p>Click on player name to get details and game record for player.</p>

EOT;
lg_html_footer();
?>
