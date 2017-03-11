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
include 'php/game.php';
include 'php/matchdate.php';
include 'php/match.php';
include 'php/team.php';

// Get who I am

try {
        $player = new Player();
        $player->fromid($userid);
}
catch (PlayerException $e) {
        $mess = $e->getMessage();
        include 'php/wrongentry.php';
        exit(0);
}

// Get message

$messid = $_GET['msgi'];

$ret = mysql_query("delete from message where ind=$messid");
if  (!$ret || mysql_affected_rows() == 0)  {
	$mess = "Could not delete message $messid";
	include 'php/wrongentry.php';
	exit(0);
}
$s = $_SERVER['SERVER_NAME'];
header("Location: http://$s/messages.php");
?>

