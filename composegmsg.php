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

$Connection = opendatabase(true);

// Get who I am

try {
	$player = new Player();
   $player->fromid($Connection->userid);
}
catch (PlayerException $e) {
	wrongentry($e->getMessage());
}

lg_html_header("Compose a message");
lg_html_nav();
print <<<EOT
<h1>Compose a message</h1>
<p>Use this form to generate a message on the internal message board visible to a user when he/she next logs in.</p>
<p>Do not use this form to arrange games for matches, instead use the messages page and select the game in question.</p>
<form action="sendgmsg.php" method="post" enctype="application/x-www-form-urlencoded">
<p>Send the message to:
<select name="recip">

EOT;
$pllist = list_players();
foreach ($pllist as $pl) {
	$pl->fetchdets();
	print <<<EOT
<option value="{$pl->selof()}">{$pl->display_name(false)}</option>

EOT;
}
print <<<EOT
</select></p>
<p>Subject: <input type="text" name="subject" size="40" /></p>
<p>Message:</p>
<br clear="all" />
<textarea name="mcont" rows="20" cols="60"></textarea>
<br clear="all" />
<p>Then <input type="submit" value="Send Message" /> when ready.</p>
</form>

EOT;
lg_html_footer();
?>
