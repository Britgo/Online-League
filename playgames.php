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
include 'php/game.php';
include 'php/matchdate.php';
include 'php/team.php';
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
	$player->fetchclub();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Player Games";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Player Results</h1>
<?php
$Name = $player->display_name(false);
print <<<EOT
<p>
These are recorded games on the league for $Name, currently
{$player->display_rank()}, of {$player->Club->display_name()}.
</p>

EOT;
$pu = $player->display_userid(0);
if (strlen($pu) != 0)
	print <<<EOT
<p>User id for the league: $pu.</p>

EOT;
$pon = $player->display_online();
if (strlen($pon) > 1)
	print <<<EOT
<p>Online name: $pon.</p>

EOT;

$pnotes = $player->display_notes();
if (strlen($pnotes) != 0)
	print <<<EOT
<h2>Notes for player</h2>
<p>$pnotes</p>

EOT;

$ret = mysql_query("select teamname from teammemb where {$player->queryof('tm')} order by teamname");
if  ($ret && mysql_num_rows($ret) > 0)  {
	print <<<EOT
<h2>Team Membership</h2>

<p>$Name is in the following:</p>
<table class="resultsb">

EOT;
	while ($row = mysql_fetch_array($ret))  {
		$team = new Team($row[0]);
		$team->fetchdets();
		print "<tr><td>{$team->display_name(true)}</td></tr>\n";
	}
	print <<<EOT
</table>

EOT;
}
else {
	print "<p>$Name is not in any teams.</p>\n";
}

$total_games = $player->played_games();
$current_games = $player->played_games(true);
if ($total_games == 0)  {
	print <<<EOT
<p>
Sorry but there do not seem to be any recorded completed games for $Name.
</p>

EOT;
}
else {
	if ($total_games != $current_games)  {
		print <<<EOT
<h2>Current Season</h2>

EOT;
		if ($current_games == 0)
			print <<<EOT
<p>No games played yet by $Name in current season</p>

EOT;
		else  {
			$wg = $player->won_games(true);
			$dg = $player->drawn_games(true);
			$lg = $player->lost_games(true);
			print <<<EOT
<p>
Record is Played: $current_games Won: $wg Drawn: $dg Lost: $lg.
</p>
<img src="php/piewdl.php?w=$wg&d=$dg&l=$lg">
<br />

EOT;
		}
		print <<<EOT
<h2>Overall record</h2>

EOT;
	}	
	$wg = $player->won_games();
	$dg = $player->drawn_games();
	$lg = $player->lost_games();
	print <<<EOT
<p>
Overall record is Played: $total_games Won: $wg Drawn: $dg Lost: $lg.
</p>
<img src="php/piewdl.php?w=$wg&d=$dg&l=$lg">
<br />
<table class="resultsb">
<tr>
<th>Date</th>
<th>Team</th>
<th>Colour</th>
<th>Opponent</th>
<th>Team</th>
<th>Outcome</th>
<th>Detail</th>
</tr>

EOT;
	// Fetch game record
	
	$ret = mysql_query("select ind from game where result!='N' and (({$player->queryof('w')}) or ({$player->queryof('b')})) order by matchdate");
	if ($ret && mysql_num_rows($ret))

		while ($row = mysql_fetch_assoc($ret)) {
			try  {
				$g = new Game($row["ind"]);
				$g->fetchdets();
			}
			catch (GameException $e) {
				print "<tr><td colspan=7>Cannot find game</td></tr>\n";
				continue;
			}
			print "<tr><td>{$g->date_played()}</td>\n";
			
			// If player is white extract right fields
			
			if ($g->Wplayer->is_same($player))  {
				print <<<EOT
<td>{$g->Wteam->display_name()}</td>
<td>White</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bteam->display_name()}</td>

EOT;
				switch ($g->Result) {
				default: $r = '?'; break;
				case 'W': $r = "Won"; break;
				case 'J': $r = "Jigo"; break;
				case 'B': $r = "Lost"; break;
				}
				print "<td>$r</td>\n";			
			}
			else {
			
				// If player is black extract right fields
				
				print <<<EOT
<td>{$g->Bteam->display_name()}</td>
<td>Black</td>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wteam->display_name()}</td>

EOT;
				switch ($g->Result) {
				default: $r = '?'; break;
				case 'W': $r = "Lost"; break;
				case 'J': $r = "Jigo"; break;
				case 'B': $r = "Won"; break;
				}
				print "<td>$r</td>\n";			
			}
			print "<td>{$g->display_result()}</td></tr>\n";
		}  // End of while
	print <<<EOT
	</table>
	<p>You can click on the name of each opponent to see the record for that opponent.
	</p>

EOT;
}	// End if "if any games played" case

// Contact details rearranged so they always show

if ($logged_in) {
	$em = $player->display_email_link();
	$ph = $player->display_phone(true);
		print <<<EOT
<h2>Player Contact Information - $Name</h2>

EOT;
	if (strlen($em) != 0 || strlen($ph) != 0)  {
		if (strlen($em) != 0)
			print <<<EOT
<p>Email address: $em.</p>

EOT;
		if (strlen($ph) != 0)
			print <<<EOT
<p>Phone number(s): $ph.</p>

EOT;
	}
	else
		print <<<EOT
<p>Sorry, we don't have any contact information for $Name.</p>

EOT;
}
?>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>
</div>
</div>
</body>
</html>
