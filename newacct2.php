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

// Clog up the works for spammers

if (isset($_POST["turnoff"]) || !isset($_POST["turnon"]))  {
	system("sleep 60");
	exit(0);
}
include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/genpasswd.php';
include 'php/newaccemail.php';
include 'php/assildiv.php';

$Connection = opendatabase();

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

if  (strlen($playname) == 0)
   wrongentry("No player name given");

if  (strlen($userid) == 0)
   wrongentry("No user name given");

//  Get player name and check he doesn't clash

try {
	$player = new Player($playname);
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}

$ret = $Connection->query("SELECT first,last FROM player WHERE {$player->queryof()}");
if ($ret && $ret->num_rows != 0)
	clash_item("name", $player->display_name(false));

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = $Connection->real_escape_string($value);
	$ret = $Connection->query("SELECT $column FROM player WHERE $column='$qvalue'");
	if ($ret && $ret->num_rows != 0)
		clash_item("$column", $value);
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
lg_html_header("New account $userid created OK");
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>Your account $userid has been successfully created and you should be receiving
a confirmatory email.</p>

EOT;
lg_html_footer();
?>
