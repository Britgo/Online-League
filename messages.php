<?php
//   Copyright 2013-2021 John Collins

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
include 'php/team.php';
include 'php/match.php';
include 'php/matchdate.php';
include 'php/game.php';

$Connection = opendatabase(true);
function listmessages($userid, $sent)
{
	if ($sent)  {
		$searchid = 'touser';
		$fetchid = 'fromuser';
		$msgt = "Sent";
		$fl = "&sent=y";
		$th = "To";
	}
	else  {
		$searchid = 'fromuser';
		$fetchid = 'touser';
		$msgt = "Received";
		$fl = "";
		$th = "From";
	}
	print "<h1>$msgt messages</h1>";
	$Sun = $Connection->real_escape_string($userid);
	$ret = $Connection->query("SELECT $searchid,created,matchind,gameind,subject,hasread,ind FROM message WHERE $fetchid='$Sun' ORDER BY created,subject");
	if (!$ret || $ret->num_rows == 0)  {
		print "<p>No messages found.</p>\n";
	}
	else  {
		print <<<EOT
<table class="resultsb">
<tr>
	<th>$th</th>
	<th>Date</th>
	<th>Time</th>
	<th>Match</th>
	<th>Game</th>
	<th>Subject</th>
EOT;
		while ($row = $ret->fetch_assoc())  {
			$fu = $row[$searchid];
			$cr = $row["created"];
			$mid = $row["matchind"];
			$gid = $row["gameind"];
			$subj = $row["subject"];
			$qsubj = htmlspecialchars($subj);
			$hasr = $row["hasread"];
			$ind = $row["ind"];
			$fp = new Player();
			$fp->fromid($fu);
			$pre = $post = "";
			if (!$hasr)  {
				$pre = "<strong>";
				$post = "</strong>";
			}
			if  (preg_match("/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)/", $cr, $matches))  {
				$dat = $matches[3] . '/' . $matches[2] . '/' . $matches[1];
				$tim = $matches[4] . ':' . $matches[5] . ':' . $matches[6];
			}
			else {
				$dat = $tim = $cr;
			}
			$am = $mid == 0? "-": "About match";
			$ag = $gid == 0? "-": "About game";
			print <<<EOT
<tr>
	<td>$pre{$fp->display_name()}$post</td>
	<td>$pre$dat$post</td>
	<td>$pre$tim$post</td>
	<td>$pre$am$post</td>
	<td>$pre$ag$post</td>
	<td><a href="dispmessage.php?msgi=$ind$fl">$pre$qsubj$post</a></td>
</tr>

EOT;
		}
		print "</table>\n";
	}
}

try {
        $player = new Player();
        $player->fromid($Connection->userid);
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}

// Get the teams this player is captain of

try {
	$captain_of = list_teams_captof($player);
}
catch (TeamException $e) {
   wrongentry($e->getMessage());
}

// Get matches we might want to send messages about.

$capt_matches = array();
if  (count($captain_of) != 0)  {

	// Do each team in turn it's easier to code

	foreach ($captain_of as $team) {
		$ret = $Connection->query("SELECT ind FROM lgmatch WHERE (result='N' OR result='P') AND ({$team->queryof('hteam')} OR {$team->queryof('ateam')}) ORDER BY matchdate");
		if ($ret && $ret->num_rows > 0)  {
			while ($row = $ret->fetch_array())  {
				try {
					$mtch = new Match($row[0]);
					$mtch->fetchdets();
					$mtch->fetchteams();
					$mtch->fetchgames();
					array_push($capt_matches, $mtch);
				}
				catch (MatchException $e) {
					continue;
				}
			}
		}
	}
}

$Qun = htmlspecialchars($Connection->username);
$Sun = $Connection->real_escape_string($Connection->userid);
lg_html_header("Messages for $Qun");
lg_html_nav();

// List received messages and sent messages.

listmessages($Connection->userid, false);
listmessages($Connection->userid, true);

// List O/S matches for team captain if this player is such

if  (count($capt_matches) != 0)  {
	print <<<EOT
<h1>Outstanding matches</h1>
<p>You might want to send the opposing team captain a message about
one of the following outstanding matches.</p>
<table class="showmatch">
<tr><th>Team A</th><th>Capt</th><th>Team B</th><th>Capt</th><th>State</th><th>Send</th></tr>

EOT;
	foreach ($capt_matches as $mtch) {
		if ($mtch->is_allocated())  {
			$state = "Unplayed";
		}
		else {
			$state = "Unallocated";
		}
		print <<<EOT
<tr>
	<td>{$mtch->Hteam->display_name(true)}</td>
	<td>{$mtch->Hteam->display_captain(true)}</td>
	<td>{$mtch->Ateam->display_name(true)}</td>
	<td>{$mtch->Ateam->display_captain(true)}</td>
	<td>$state</td>
	<td><a href="composemsg.php?{$mtch->urlof()}">Message</a></td>
</tr>

EOT;
	}
	print "</table>\n";
}
print "<h1>Outstanding games</h1>\n";

// Now for user's games

$osgames = array();
$ret = $Connection->query("SELECT ind FROM game WHERE result='N' AND (({$player->queryof('w')}) OR ({$player->queryof('b')})) ORDER BY matchdate");
if ($ret && $ret->num_rows > 0)  {
	while ($row = $ret->fetch_array())  {
		try {
			$g = new Game($row[0]);
			$g->fetchdets();
			if (!$g->Wteam || !$g->Bteam)
				continue;
			array_push($osgames, $g);
		}
		catch (GameException $e) {
			continue;
		}
	}
}
if (count($osgames) == 0)
	print <<<EOT
<p>You currently do not have any outstanding games to play.</p>

EOT;
else  {
	print <<<EOT
<p>You might want to send a message about one of the following
pending games.</p>
<table class="showmatch">
<tr>
<th colspan="3" align="center">White</th>
<th colspan="3" align="center">Black</th></tr>
<tr>
<th>Player</th>
<th>Online name</th>
<th>Team</th>
<th>Player</th>
<th>Online name</th>
<th>Team</th>
<th>Message</th>
</tr>

EOT;
	foreach ($osgames as $g) {
		print <<<EOT
<tr>
<td>{$g->Wplayer->display_name()}</td>
<td>{$g->Wplayer->display_online()}</td>
<td>{$g->Wteam->display_name()}</td>
<td>{$g->Bplayer->display_name()}</td>
<td>{$g->Bplayer->display_online()}</td>
<td>{$g->Bteam->display_name()}</td>
<td><a href="composemsg.php?{$g->urlof()}">Send</a></td>
</tr>

EOT;
	}
print <<<EOT
</table>

EOT;
}
print <<<EOT
<h1>General message</h1>
<p>If you want to send someone a new message about any subject,
<a href="composegmsg.php">Click Here</a>.</p>

EOT;
lg_html_footer();
?>
