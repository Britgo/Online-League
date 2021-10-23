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

include 'php/html_blocks.php';
include 'php/error_handling.php';
include 'php/connection.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/assildiv.php';

$Connection = opendatabase(true);

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = $Connection->real_escape_string($value);
	$ret = $Connection->query("SELECT $column FROM player WHERE $column='$qvalue'");
	if ($ret && $ret->num_rows != 0)
			clash_item($column, $value);
}

function checkname($newplayer) {
	$ret = $Connection->query("SELECT first,last FROM player WHERE {$newplayer->queryof()}");
	if ($ret && $ret->num_rows != 0)
		clash_item("name", "{$newplayer->First} {$newplayer->Last}");
}

$playname = $_POST["playname"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$kgs = $_POST["kgs"];
$igs = $_POST["igs"];
$club = $_POST["club"];
$rank = $_POST["rank"];
$passw = $_POST["passw"];
$okem = isset($_POST["okem"]);
$trivia = isset($_POST["trivia"]);
$notes = $_POST["notes"];
$latest = $_POST["latesttime"];

try {
	$origplayer = new Player();
	$origplayer->frompost();
	$origplayer->fetchdets();
}
catch (PlayerException $e) {
   wrongentry($e->getMessage());
}

// Check name changes and perform update if applicable
// Note that the "updatename" function does any consequent
// updates like changing team captain name if the player is a
// team captain.

$chname = false;
$newplayer = new Player($playname);
if  (!$origplayer->is_same($newplayer))  {
	checkname($newplayer);
	$origplayer->updatename($newplayer);
	$chname = true;
}

if ($origplayer->KGS != $kgs) {
	checkclash("kgs", $kgs);
	$origplayer->KGS = $kgs;
}
if ($origplayer->IGS != $igs) {
	checkclash("igs", $igs);
	$origplayer->IGS = $igs;
}

$origplayer->Rank = new Rank($rank);
$origplayer->Club = new Club($club);
$origplayer->Email = $email;
$origplayer->OKemail = $okem;
$origplayer->Trivia = $trivia;
$origplayer->Phone = $phone;
$origplayer->Notes = $notes;
$origplayer->Latestcall = $latest == "None"? "": $latest;

if ($origplayer->ILdiv == 0)  {
	if (isset($_POST["join"]))
		$origplayer->ILdiv = assign_ildiv($rank);
}
else  if  (!$origplayer->ILpaid  &&  !isset($_POST["stayin"]))
	$origplayer->ILdiv = 0;
$origplayer->update();
if (strlen($passw) != 0  &&  $passw != $origplayer->get_passwd())
	$origplayer->set_passwd($passw);

lg_html_header("Player details updated OK");
lg_html_nav();
print <<<EOT
<h1>Player details updated OK</h1>
<p>Updated player details successfully.</p>

EOT;
if ($chname)
	print <<<EOT
<p>As you changed your name, you should probably logout and log back in again using the
menu on the left. This will reset any "cookies" with your original name in.</p>

EOT;
lg_html_footer();
?>
