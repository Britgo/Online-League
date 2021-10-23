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
$messid = $_POST["msgi"];
$mid = $_POST["mi"];
$gid = $_POST["gn"];
$subj = $_POST["subject"];
$msgt = $_POST["mcont"];
$ret = $Connection->query("SELECT fromuser FROM message WHERE ind=$messid");
if  (!$ret || $ret->num_rows == 0)
   wrongentry("Cannot find mess id $messid");
$row = $ret->fetch_array();
$recipid = $row[0];
$recip = new Player();
$recip->fromid($recipid);
$qfrom = $Connection->real_escape_string($player->Userid);
$qto = $Connection->real_escape_string($recipid);
$qsubj = $Connection->real_escape_string($subj);
$qmsgt = $Connection->real_escape_string($msgt);
$Connection->query("INSERT INTO message (fromuser,touser,created,gameind,matchind,subject,contents) VALUES ('$qfrom','$qto',now(),$gid,$mid,'$qsubj','$qmsgt')");
lg_html_header("Reply Sent");
lg_html_nav();
print <<<EOT
<h1>Reply Sent</h1>
<p>I believe your reply was sent OK to {$recip->display_name()}.</p>
<p><a href="messages.php">Click Here</a> to go back to messages.</p>

EOT;
lg_html_footer();
?>
