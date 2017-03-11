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

// This is invoked from a subwindow so we don't do the frame emulation stuff.

include 'php/session.php';
include 'php/opendatabase.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/club.php';

try {
	$player = new Player();
	$player->fromid($_GET['uid']);
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$em = $player->Email;
$pw = $player->get_passwd();

if (strlen($em) == 0)  {
	$Title = "No email address";
	$Mess = "Player {$player->display_name(false)} has no email address set up.";
}
elseif (strlen($pw) == 0)  {
	$Title = "No password";
	$Mess = "Player {$player->display_name(false)} has no password set.";
}
else {
	$Title = "Reminder sent";
	$Mess = "Reminder was sent OK.";
	$fh = popen("mail -s 'Go League email - password reminder' $em", "w");
	fwrite($fh, "Your userid is {$player->Userid}.\n");
	fwrite($fh, "Your password is $pw\n");
	pclose($fh);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
include 'php/head.php';
print <<<EOT
<body>
<h1>$Title</h1>
<p>$Mess</p>
EOT;
?>
<p>Please click <a href="javascript:self.close();">here</a> to close this window.</p>
</body>
</html>
