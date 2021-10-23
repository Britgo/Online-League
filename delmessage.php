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
include 'php/game.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/team.php';

$Connection = opendatabase(true);

// Get who I am

try {
	$player = new Player();
   $player->fromid($Connection->userid);
}
catch (PlayerException $e) {
	wrongentry($e->getMessage());
}

// Get message

$messid = $_GET['msgi'];

$ret = $Connection->query("DELETE FROM message WHERE ind=$messid");
if  (!$ret || $Connection->affected_rows == 0)
	wrongentry("Could not delete message $messid");
$s = $_SERVER['SERVER_NAME'];
header("Location: http://$s/messages.php");
?>
