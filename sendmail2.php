<?php
//   Copyright 2011-2021 John Collins

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

//   No frame-ish stuff - done in new window

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';

$Connection = opendatabase(true);

try {
	$via = $_POST["via"];
	switch ($via) {
	default:
		$player = new Player();
		$player->frompost();
		$player->fetchdets();
		$name = $player->display_name(false);
		$dest = $player->Email;
		break;
	case "club":
		$club = new Club();
		$club->frompost();
		$club->fetchdets();
		$name = $club->display_contact();
		$dest = $club->Contactemail;
		break;
	}
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}
catch (ClubException $e) {
   wrongentry($e->getMessage());
}
if (strlen($dest) == 0)
   database_error("No email dest");

$subj = $_POST["subject"];
$emailrep = $_POST["emailrep"];
$mess = $_POST["messagetext"];
$rt = "";
if (strlen($emailrep) != 0)
	$rt = "REPLYTO='$emailrep' ";
$fh = popen("{$rt}mail -s 'Go League email - $subj' $dest", "w");
fwrite($fh, "$mess\n");
pclose($fh);
lg_html_header("Message Sent to $nam");
lg_html_nav();
print <<<EOT
<h1>Message sent to $name</h1>
<p>I think your message was sent OK to $name.</p>
<p>Please click <a href="javascript:self.close();">here</a> to close this window.</p>

EOT;
lg_html_footer();
?>
