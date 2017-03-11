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
include 'php/player.php';
include 'php/game.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/team.php';

function concmatch($id, $mp) {
	if ($id == 0)
		return;
	print <<<EOT
<tr>
	<td><strong>Concerning match:</strong</td>
	<td>{$mp->Hteam->display_name(true)} -v- {$mp->Ateam->display_name(true)}</td>
</tr>

EOT;
}

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

// Get message

$messid = $_GET['msgi'];
$sent = isset($_GET['sent']);
if  (!preg_match('/^\d+$/', $messid))  {
	$mess = "Inappropriate message id $messid";
	include 'php/wrongentry.php';
	exit(0);
}

$ret = mysql_query("select fromuser,touser,created,matchind,gameind,subject,hasread,contents from message where ind=$messid");
if  (!$ret || mysql_num_rows($ret) == 0)  {
	$mess = "Could not find message $messid";
	include 'php/wrongentry.php';
	exit(0);
}
$row = mysql_fetch_assoc($ret);
$fu = $row["fromuser"];
$tu = $row["touser"];
$cr = $row["created"];
$mid = $row["matchind"];
$gid = $row["gameind"];
$subj = $row["subject"];
$hsubj = htmlspecialchars($subj);
$hasr = $row["hasread"];
$cont = $row["contents"];
try {
	$fp = new Player();
	$fp->fromid($fu);
}
catch (PlayerException $e) {
	$mess = "Unknown sender id $fu";
	include 'php/wrongentry.php';
	exit(0);
}
try {
	$tp = new Player();
	$tp->fromid($tu);
}
catch (PlayerException $e) {
	$mess = "Unknown recipient id $tu";
	include 'php/wrongentry.php';
	exit(0);
}
if (!$hasr && !$sent)
	mysql_query("update message set hasread=1 where ind=$messid");

if  (preg_match("/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+):(\d+)/", $cr, $matches))  {
	$dat = $matches[3] . '/' . $matches[2] . '/' . $matches[1];
	$tim = $matches[4] . ':' . $matches[5] . ':' . $matches[6];
}
else {
	$dat = $tim = $cr;
}
if ($mid != 0) {
	$mtch = new Match($mid);
	try {
		$mtch->fetchdets();
		$mtch->fetchteams();
	}
	catch (MatchException $e) {
		$mid = 0;
	}
}
if ($gid != 0)  {
	$gam = new Game($gid);
	try {
		$gam->fetchdets();
		$gmtch = new Match($gam->Matchind);
		$gmtch->fetchdets();
		$gmtch->fetchteams();
	}
	catch (GameException $e) {
		$gid = 0;
	}
	catch (MatchException $e) {
		$gid = 0;
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Display Message";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
$mhdr = $hasr? "Message": "New message";
print <<<EOT
<h1>$mhdr</h1>

<table class="resultsb">
<tr>
	<td><strong>From:</strong></td>
	<td>{$fp->display_name()}<td>
</tr>
<tr>
	<td><strong>To:</strong></td>
	<td>{$tp->display_name()}<td>
</tr>
<tr>
	<td><strong>Sent:</strong</td>
	<td>$dat at $tim</td>
</tr>
<tr>
	<td><strong>Subject:</strong</td>
	<td>$hsubj</td>
</tr>

EOT;

concmatch($mid, $mtch);
concmatch($gid, $gmtch);

$hcont = htmlspecialchars($cont);
$hcont = preg_replace("/(\r\n)+$/", "", $hcont);
$hcont = preg_replace("/(\r?\n){2,}/", "</p>\n<p>", $hcont);
print <<<EOT
</table>
<p>$hcont</p>

<h2>Delete message</h2>
<p><a href="delmessage.php?msgi=$messid">Click here</a> if you want to delete this message.</p>

EOT;

// If it's a received message, offer the chance to reply  

if (!$sent)  {
	print  "<h2>Send reply</h2>\n";
	$subj = preg_replace("/^Re:\s*/i", "", $subj);
	$subj = "Re: $subj";
	$hsubj = htmlspecialchars($subj);
	print <<<EOT
<form action="sendreply.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="msgi" value="$messid" />
<input type="hidden" name="mi" value="$mid" />
<input type="hidden" name="gn" value="$gid" />
<p>Subject: <input type="text" name="subject" value="$hsubj" size="40" /></p>
<p>Message:</p>
<br clear="all" />
<textarea name="mcont" rows="20" cols="60"></textarea>
<br clear="all" />
<p>Then <input type="submit" value="Send Reply" /> when ready.</p>
</form>
EOT;
}
?>
</div>
</div>
</body>
</html>
