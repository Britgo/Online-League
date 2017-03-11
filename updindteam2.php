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
include 'php/team.php';

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from team where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		include 'php/nameclash.php';
		exit(0);
	}
}

function checkname($newteam) {
	$ret = mysql_query("select name from team where {$newteam->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "name";
		$value = $newteam->display_name();
		include 'php/nameclash.php';
		exit(0);
	}
}

$action = substr($_POST["subm"], 0, 1);
$teamname = $_POST["teamname"];
$teamdescr = $_POST["teamdescr"];
$teamdiv = $_POST["division"];
$teamcapt = $_POST["captain"];

if (!preg_match("/(.*):(.*)/", $teamcapt, $matches))  {
		include 'php/wrongentry.php';
		exit(0);
}

$captfirst = $matches[1];
$captlast = $matches[2];

switch ($action) {
case 'A':
	if (strlen($teamname) == 0)  {
		$mess = "No team name?";
		include 'php/wrongentry.php';
		exit(0);
	}
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
		$mess = $e->getMessage();
		include 'php/wrongentry.php';
		exit(0);
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Team update complete";
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
<p>Click <a href="teamsupd.php">here</a> to return to the team update menu.</p>
</div>
</div>
</body>
</html>
