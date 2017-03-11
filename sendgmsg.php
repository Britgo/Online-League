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
        $recip = new Player();
		  $recip->fromsel($_POST["recip"]);
		  $recip->fetchdets();
}
catch (PlayerException $e) {
        $mess = $e->getMessage();
        include 'php/wrongentry.php';
        exit(0);
}
$subj = $_POST["subject"];
$msgt = $_POST["mcont"];
$mid = $gid = 0;
if (isset($_POST["mi"]))
	$mid = $_POST["mi"];
if (isset($_POST["gn"]))
	$gid = $_POST["gn"];
$qfrom = mysql_real_escape_string($player->Userid);
$qto = mysql_real_escape_string($recip->Userid);
$qsubj = mysql_real_escape_string($subj);
$qmsgt = mysql_real_escape_string($msgt);
mysql_query("insert into message (fromuser,touser,created,gameind,matchind,subject,contents) values ('$qfrom','$qto',now(),$gid,$mid,'$qsubj','$qmsgt')");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Message Sent";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
print <<<EOT
<h1>Message Sent</h1>
<p>I believe your message was sent OK to {$recip->display_name()}.</p>
<p><a href="messages.php">Click Here</a> to go back to messages.</p>

EOT;
?>
</div>
</div>
</body>
</html>