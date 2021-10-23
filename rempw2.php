<?php
//   Copyright 2009-2021 John Collins

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
include 'php/rank.php';
include 'php/player.php';
include 'php/club.php';

$Connection = opendatabase(true);
try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
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
lg_html_header("Remind password completed");
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>$Mess</p>

EOT;
lg_html_footer();
?>
