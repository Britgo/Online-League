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

include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendatabase.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/player.php';
include 'php/genpasswd.php';
include 'php/newaccemail.php';
include 'php/assildiv.php';

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

function checkname($newplayer) {
	$ret = mysql_query("select first,last from player where {$newplayer->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "name";
		$value = $newplayer->display_name(false);
		include 'php/nameclash.php';
		exit(0);
	}
}

$action = substr($_POST["subm"], 0, 1);
$playname = $_POST["playname"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$userid = $_POST["userid"];
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
	if (strlen($playname) == 0 || strlen($userid) == 0)  {
		include 'php/wrongentry.php';
		exit(0);
	}
	$player = new Player($playname);
	checkname($player);
	checkclash('user', $userid);
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
	$player->Userid = $userid;
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
	newaccemail($email, $userid, $passw);
	break;
default:
	try {
		$origplayer = new Player();
		$origplayer->frompost();
		$origplayer->fetchdets();
	}
	catch (PlayerException $e) {
		$mess = $e->getMessage();
		include 'php/wrongentry.php';
		exit(0);
	}
	
	// Check name changes
	
	$newplayer = new Player($playname);
	if  (!$origplayer->is_same($newplayer))  {
		checkname($newplayer);
		$origplayer->updatename($newplayer);
	}
	
	// Check user kgs and igs clashes
	
	if ($origplayer->Userid != $userid)  {
		checkclash('user', $userid);
		$origplayer->Userid = $userid;
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Completed player update";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>$Title</h1>
<p>$Title.</p>

EOT;
?>
<p>Click <a href="playupd.php">here</a> to return to the player update menu.</p>
</div>
</div>
</body>
</html>
