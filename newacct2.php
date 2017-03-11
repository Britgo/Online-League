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

// Clog up the works for spammers

if (isset($_POST["turnoff"]) || !isset($_POST["turnon"]))  {
	system("sleep 60");
	exit(0);
}

include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/genpasswd.php';
include 'php/newaccemail.php';
include 'php/assildiv.php';

$playname = $_POST["playname"];
$userid = $_POST["userid"];
$passw = $_POST["passw"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$club = $_POST["club"];
$rank = $_POST["rank"];
$okem = isset($_POST["okem"]);
$trivia = isset($_POST["trivia"]);
$kgs = $_POST["kgs"];
$igs = $_POST["igs"];
$joinil = isset($_POST["join"]);
$notes = $_POST["notes"];
$latest = $_POST["latesttime"];

if  (strlen($playname) == 0)  {
	$mess = "No player name given";
	include 'php/wrongentry.php';
	exit(0);
}
if  (strlen($userid) == 0)  {
	$mess = "No user name given";
	include 'php/wrongentry.php';
	exit(0);
}

//  Get player name and check he doesn't clash

try {
	$player = new Player($playname);
}
catch (PlayerException $e) {
   $mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}

$ret = mysql_query("select first,last from player where {$player->queryof()}");
if ($ret && mysql_num_rows($ret) != 0)  {
	$column = "name";
	$value = $player->display_name(false);
	include 'php/nameclash.php';
	exit(0);
}

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from player where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		include 'php/nameclash.php';
		exit(0);
	}
}

// Check user name, KGS and IGS accounts (if any) don't clash

checkclash('user', $userid);
checkclash('kgs', $kgs);
checkclash('igs', $igs); 

$player->Rank = new Rank($rank);
$player->Club = new Club($club);
$player->Email = $email;
$player->OKemail = $okem;
$player->Trivia = $trivia;
$player->Phone = $phone;
$player->KGS = $kgs;
$player->IGS = $igs;
$player->Userid = $userid;
$player->Notes = $notes;
$player->Latestcall = $latest == "None"? "": $latest;
if ($joinil)
	$player->ILdiv = assign_ildiv($rank);

$player->create();

// If no password specified, invent one

if (strlen($passw) == 0)
	$passw = generate_password();

$player->set_passwd($passw);
newaccemail($email, $userid, $passw);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "New account $userid created OK";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php';
print <<<EOT
<h1>$Title</h1>
<p>Your account $userid has been successfully created and you should be receiving
a confirmatory email.</p>

EOT;
?>
</div>
</div>
</body>
</html>
