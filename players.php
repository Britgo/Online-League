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
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Players List";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Players</h1>
<?php
switch ($_GET["by"])  {
default:
	$pclub = 1;
	$order = "last,first,rank desc";
	$next = "club";
	$initials = list_player_initials();
	$byrank = 0;
	break;
case  "club":
	$pclub = 0;
	$order = "club.name,last,first,rank desc";
	$next = "rank";
	$initials = list_club_initials();
	$byrank = 0;
	break;
case  "rank":
	$pclub = 1;
	$order = "rank desc,last,first,rank desc";
	$next = "clubrank";
	$initials = list_player_ranks();
	$byrank = 1;
	break;
case  "clubrank":
	$pclub = 0;
	$order = "club.name,rank desc,last,first";
	$next = "name";
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
$ref = "<a href=\"players.php?by=$next\">";
print <<<EOT
<table class="pllist">
<tr>
<th colspan="3">&nbsp;</th>
<th colspan="4" align="center">Current</th>
<th>&nbsp;</th>
<th colspan="4" align="center">Total</th>
</tr>
<tr>
<th>${ref}Name</a></th>
<th>${ref}Rank</a></th>
<th>${ref}Online</a></th>
<th>${ref}P</a></th>
<th>${ref}W</a></th>
<th>${ref}D</a></th>
<th>${ref}L</a></th>
<td>&nbsp;</td>
<th>${ref}P</a></th>
<th>${ref}W</a></th>
<th>${ref}D</a></th>
<th>${ref}L</a></th>

EOT;
if ($pclub)
	print "<th>${ref}Club</a></th>\n";
print <<<EOT
</tr>

EOT;

$ret = mysql_query("select first,last,club.name from player,club where player.club=club.code order by $order");
if ($ret && mysql_num_rows($ret)) {
	$lclub = "not set";
	$linit = "-";
	$lrank = new Rank(4000);
	while ($row = mysql_fetch_assoc($ret)) {
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
?>
</table>
<p>Click on player name to get details and game record for player.</p>
</div>
</div>
</body>
</html>
