<?php
//   Copyright 2010 John Collins

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
try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update playing/not playing status for Team {$team->display_name()}";
include 'php/head.php';
print <<<EOT
<body>
<h1>Update Playing/not playing for Team {$team->display_name()}</h1>
EOT;
if ($team->Playing) {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as playing this season but setting
to <b>not playing</b>.
</p>
EOT;
	$v = false;
}
else {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as not playing this season.
Now setting to <b>playing</b>.
</p>

EOT;
	$v = true;
}
$team->setplaying($v);
?>
<p>
Click <a href="teamsupd.php" target="_top">here</a> to return to the team update menu.
</p>
</body>
</html>
