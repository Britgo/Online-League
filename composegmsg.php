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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Qun = htmlspecialchars($username);
$Sun = mysql_real_escape_string($userid);
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
<p>Send the message to:
<select name="recip">
<?php
$pllist = list_players();
foreach ($pllist as $pl) {
	$pl->fetchdets();
	print <<<EOT
<option value="{$pl->selof()}">{$pl->display_name(false)}</option>

EOT;
}
?>
</select></p>
<p>Subject: <input type="text" name="subject" size="40" /></p>
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