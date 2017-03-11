<?php
//   Copyright 2013 John Collins

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
include 'php/club.php';
include 'php/rank.php';
include 'php/team.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/player.php';
include 'php/game.php';

// Get who I am

try {
	$player = new Player();
   $player->fromid($userid);
}
catch (PlayerException $e) {
   $mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}

// If these refer to specific match or game get the details.

$mid = $gid = 0;
if (isset($_GET["mi"]))
	$mid = $_GET["mi"];
if (isset($_GET["gn"]))
	$gid = $_GET["gn"];
	
if ($mid == 0 && $gid == 0) {
	 $mess = "Unknown message topic";
    include 'php/wrongentry.php';
    exit(0);
}

// If it's about a match, I must be team captain of one of the teams, recipient is the one who isn't me

if ($mid != 0) {
	try {
		$match = new Match($mid);
		$match->fetchdets();
		$match->fetchteams();
	}
	catch (MatchException $e)  {
		$mess = $e->getMessage();
      include 'php/wrongentry.php';
      exit(0);
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
		$mess = $e->getMessage();
      include 'php/wrongentry.php';
      exit(0);
	}
	catch (MatchException $e)  {
		$mess = $e->getMessage();
      include 'php/wrongentry.php';
      exit(0);
	}
	$hteam = $match->Hteam;
	$ateam = $match->Ateam;
	$recip = $game->Wplayer;
	if ($recip->is_same($player))
		$recip = $game->Bplayer;
	$subj = htmlspecialchars("Game for Match: {$hteam->display_name()} -v- {$ateam->display_name()}");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Compose a message";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Compose a message</h1>
<p>Use this form to generate a message on the internal message board
visible to a user when he/she next logs in.</p>
<p>Do not use this form to arrange games for matches, instead use the messages
page and select the game in question.</p>
<form action="sendgmsg.php" method="post" enctype="application/x-www-form-urlencoded">
<?php
print <<<EOT
<input type="hidden" name="mi" value="$mid" />
<input type="hidden" name="gn" value="$gid" />

EOT;
?>
<p>Send the message to:
<select name="recip">
<?php
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

EOT;
?>
<p>Message:</p>
<br clear="all" />
<textarea name="mcont" rows="20" cols="60"></textarea>
<br clear="all" />
<p>Then <input type="submit" value="Send Message" /> when ready.</p>
</form>
</div>
</div>
</body>
</html>
