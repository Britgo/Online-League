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
include 'php/team.php';

$Connection = opendatabase(true);

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = $Connection->real_escape_string($value);
	$ret = $Connection->query("SELECT $column FROM team WHERE $column='$qvalue'");
	if ($ret && $ret->num_rows != 0)
		clash_item($column, $value);
}

function checkname($newteam) {
	$ret = $Connection->query("SELECT name FROM team WHERE {$newteam->queryof()}");
	if ($ret && $ret->num_rows != 0)
		clash_item("Team name", $newteam->display_name());
}

$action = substr($_POST["subm"], 0, 1);
$teamname = $_POST["teamname"];
$teamdescr = $_POST["teamdescr"];
$teamdiv = $_POST["division"];
$teamcapt = $_POST["captain"];

if (!preg_match("/(.*):(.*)/", $teamcapt, $matches))
	wrongentry("Mp te, ma,e");

$captfirst = $matches[1];
$captlast = $matches[2];

switch ($action) {
case 'A':
	if (strlen($teamname) == 0)
   	wrongentry("No team name?");
	$team = new Team($teamname);
	checkname($team);
	$team->Description = $teamdescr;
	$team->Division = $teamdiv;
	$team->Captain = new Player($captfirst, $captlast);
	$team->create();
	$Title = "Team {$team->display_name()} created OK";
	break;
default:
	try {
		$origteam = new Team();
		$origteam->frompost();
		$origteam->fetchdets();
	}
	catch (TeamException $e) {
	   wrongentry($e->getMessage());
	}

	// Check name changes

	$newteam = new Team($teamname);
	if  (!$origteam->is_same($newteam))  {
		checkname($newteam);
		$origteam->updatename($newteam);
	}

	$origteam->Description = $teamdescr;
	$origteam->Division = $teamdiv;
	$origteam->Captain = new Player($captfirst, $captlast);
	$origteam->update();
	$Title = "Team {$origteam->display_name()} updated OK";
	break;
}
lg_html_header($Title);
lg_html_nav();
print <<<EOT
<h1>$Title</h1>
<p>$Title.</p>
<p>Click <a href="teamsupd.php">here</a> to return to the team update menu.</p>

EOT;
lg_html_footer();
?>
