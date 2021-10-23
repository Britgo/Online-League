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
include 'php/genpasswd.php';
include 'php/newaccemail.php';
include 'php/assildiv.php';

$Connection = opendatabase(true);

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = $Connection->real_escape_string($value);
	$ret = $Connection->query("SELECT $column FROM player WHERE $column='$qvalue'");
	if ($ret && $ret->num_rows != 0)
		clash_item($column, htmlspecialchars($value));
}

function checkname($newplayer) {
	$ret = $Connection->query("SELECT first,last FROM player WHERE {$newplayer->queryof()}");
	if ($ret && $ret->num_rows != 0)
		clash_item("name", $newplayer->display_name(false));
}

$action = substr($_POST["subm"], 0, 1);
$playname = $_POST["playname"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$fuserid = $_POST["userid"];
$kgs = $_POST["kgs"];
$igs = $_POST["igs"];
$club = $_POST["club"];
$rank = $_POST["rank"];
$admin = $_POST["admin"];
$passw = $_POST["passw"];
$okem = isset($_POST["okem"]);
$bgamemb = isset($_POST["bgamemb"]);
$ildiv = $_POST["ildiv"];
$ilpaid = isset($_POST["ilpaid"]);
$notes = $_POST["notes"];
$latest = $_POST["latesttime"];

switch ($action) {
case 'A':
	if (strlen($playname) == 0 || strlen($fuserid) == 0)
		wrongentry();

	$player = new Player($playname);
	checkname($player);
	checkclash('user', $fuserid);
	checkclash('kgs', $kgs);
	checkclash('igs', $igs);
	$player->Rank = new Rank($rank);
	$player->Club = new Club($club);
	$player->Email = $email;
	$player->OKemail = $okem;
	$player->BGAmemb = $bgamemb;
	$player->Phone = $phone;
	$player->Notes = $notes;
	$player->Latestcall = $latest == "None"? "": $latest;
	$player->KGS = $kgs;
	$player->IGS = $igs;
	// Force Admin priv to N unless Super-admin
	$player->Admin = $userpriv != 'SA'? $admin: 'N';
	$player->Userid = $fuserid;
	// New player gives div num of -1 to indicate calculate it.
	if ($ildiv < 0)
		$ildiv = assign_ildiv($rank);
	$player->ILdiv = $ildiv;
	$player->create();
	if  ($ilpaid)
		$player->setpaid(true);
	// If no password specified, invent one
	if (strlen($passw) == 0)
		$passw = generate_password();
	$player->set_passwd($passw);
	$Title = "Player {$player->display_name(false)} created OK";
	newaccemail($email, $fuserid, $passw);
	break;
default:
	try {
		$origplayer = new Player();
		$origplayer->frompost();
		$origplayer->fetchdets();
	}
	catch (PlayerException $e) {
   	wrongentry($e->getMessage());
	}

	// Check name changes

	$newplayer = new Player($playname);
	if  (!$origplayer->is_same($newplayer))  {
		checkname($newplayer);
		$origplayer->updatename($newplayer);
	}

	// Check user kgs and igs clashes

	if ($origplayer->Userid != $fuserid)  {
		checkclash('user', $fuserid);
		$origplayer->Userid = $fuserid;
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
	$origplayer->Phone = $phone;
	$origplayer->Notes = $notes;
	$origplayer->Latestcall = $latest == "None"? "": $latest;
	$origplayer->OKemail = $okem;
	$origplayer->BGAmemb = $bgamemb;
	$origplayer->ILdiv = $ildiv;
	// Leave priv alone unless super-admin
	if ($userpriv == 'SA')
		$origplayer->Admin = $admin;
	$origplayer->update();
	if ($origplayer->ILpaid)  {
		if  (!$ilpaid)
			$origplayer->setpaid(false);
	}
	else  if  ($ilpaid)
		$origplayer->setpaid(true);
	if (strlen($passw) != 0  &&  $passw != $origplayer->get_passwd())
		$origplayer->set_passwd($passw);
	$Title = "Player {$origplayer->display_name(false)} updated OK";
	break;
}
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>$Title.</p>
<p>Click <a href="playupd.php">here</a> to return to the player update menu.</p>

EOT;
lg_html_footer();
?>
