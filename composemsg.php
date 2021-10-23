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
include 'php/team.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/player.php';
include 'php/game.php';

$Connection = opendatabase(true);

// Get who I am

try {
	$player = new Player();
   $player->fromid($Connection->userid);
}
catch (PlayerException $e) {
	wrongentry($e->getMessage());
}

// If these refer to specific match or game get the details.

$mid = $gid = 0;
if (isset($_GET["mi"]))
	$mid = $_GET["mi"];
if (isset($_GET["gn"]))
	$gid = $_GET["gn"];

if ($mid == 0 && $gid == 0) {
	 wrongentry("Unknown message topic");
}

// If it's about a match, I must be team captain of one of the teams, recipient is the one who isn't me

if ($mid != 0) {
	try {
		$match = new Match($mid);
		$match->fetchdets();
		$match->fetchteams();
	}
	catch (MatchException $e)  {
		wrongentry($e->getMessage());
	}
	$hteam = $match->Hteam;
	$ateam = $match->Ateam;
	$recip = $hteam->Captain;
	if ($recip->is_same($player))
		$recip = $ateam->Captain;
	$subj = htmlspecialchars("Match: {$hteam->display_name()} -v- {$ateam->display_name()}");
}
else  {
	// About game, recipient isn't me
	try {
		$game = new Game($gid);
		$game->fetchdets();
		$match = new Match($game->Matchind);
		$match->fetchdets();
		$match->fetchteams();		// Prefer to get from match although should be the same
	}
	catch (GameException $e)  {
		wrongentry($e->getMessage());
	}
	catch (MatchException $e)  {
		wrongentry($e->getMessage());
	}
	$hteam = $match->Hteam;
	$ateam = $match->Ateam;
	$recip = $game->Wplayer;
	if ($recip->is_same($player))
		$recip = $game->Bplayer;
	$subj = htmlspecialchars("Game for Match: {$hteam->display_name()} -v- {$ateam->display_name()}");
}
lg_html_header("Compose a message");
lg_html_nav();
print <<<EOT
<h1>Compose a message</h1>
<p>Use this form to generate a message on the internal message board
visible to a user when he/she next logs in.</p>
<p>Do not use this form to arrange games for matches, instead use the messages
page and select the game in question.</p>
<form action="sendgmsg.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="mi" value="$mid" />
<input type="hidden" name="gn" value="$gid" />

<p>Send the message to:
<select name="recip">

EOT;
$pllist = list_players();
foreach ($pllist as $pl) {
	$pl->fetchdets();
	$sel = $pl->is_same($recip)? " selected": "";
	print <<<EOT
<option value="{$pl->selof()}"$sel>{$pl->display_name(false)}</option>

EOT;
}
print <<<EOT
</select></p>
<p>Subject: <input type="text" name="subject" value="$subj" size="40" /></p>
<p>Message:</p>
<br clear="all" />
<textarea name="mcont" rows="20" cols="60"></textarea>
<br clear="all" />
<p>Then <input type="submit" value="Send Message" /> when ready.</p>
</form>

EOT;
lg_html_footer();
?>
