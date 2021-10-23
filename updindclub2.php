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

$Connection = opendatabase(true);
function checkclash($code) {
	$club = new Club($code);
	$ret = $Connection->query("SELECT code FROM club WHERE {$club->queryof()}");
	if ($ret && $ret->num_rows != 0)
		clash_item("code", $code);
}

$action = substr($_POST["subm"], 0, 1);
$newcode = $_POST["clubcode"];
$newname = $_POST["clubname"];
$contname = $_POST["contname"];
$contphone = $_POST["contphone"];
$contemail = $_POST["contemail"];
$website = $_POST["website"];
$night = $_POST["night"];
$sch = isset($_POST["schools"]);

if (preg_match("/(.*)\s+(.*)/", $contname, $matches)) {
	$contfirst = $matches[1];
	$contlast = $matches[2];
}
else  {
	$contfirst = "";
	$contlast = "";
}
if  (preg_match("/^http:\/\/(.*)/", $website, $matches))  {
	$website = $matches[1];
}

switch ($action) {
case 'A':
	if (strlen($newcode) == 0)
		wrongentry("No action");
	checkclash($newcode);
	$club = new Club($newcode);
	$club->Name = $newname;
	$club->Contactfirst = $contfirst;
	$club->Contactlast = $contlast;
	$club->Contactemail = $contemail;
	$club->Contactphone = $contphone;
	$club->Website = $website;
	$club->Night = $night;
	$club->Schools = $sch;
	$club->create();
	$Title = "Club {$club->display_name()} created OK";
	break;
default:
	try {
		$club = new Club();
		$club->frompost();
		$club->fetchdets();
	}
	catch (ClubException $e) {
   	wrongentry($e->getMessage());
	}
	// If name has changed, check it doesn't clash
	if ($newcode != $club->Code)  {
		checkclash($newcode);
		$qcode = $Connection->real_escape_string($newcode);
		$Connection->query("UPDATE club SET code='$qcode' WHERE {$club->queryof()}");
		$club->Code = $newcode;
	}
	$club->Name = $newname;
	$club->Contactfirst = $contfirst;
	$club->Contactlast = $contlast;
	$club->Contactemail = $contemail;
	$club->Contactphone = $contphone;
	$club->Website = $website;
	$club->Night = $night;
	$club->Schools = $sch;
	$club->update();
	$Title = "Club {$club->display_name()} updated OK";
	break;
}
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>$Title.</p>
<p>Click <a href="clubupd.php">here</a> to return to the club update menu.</p>

EOT;
lg_html_footer();
?>
