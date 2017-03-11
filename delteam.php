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

try {
	$team = new Team();
	$team->fromget();
	$team->fetchdets();
}
catch (TeamException $e) {
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from team where {$team->queryof()}");
if (!$ret) {
	$mess = "Cannot delete team";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No team deleted";
	include 'php/dataerror.php';
	exit(0);
}
//  Next is no error if nothing gets deleted.
mysql_query("delete from teammemb where {$team->queryof('teamname')}");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Deletion of {$team->display_name()} complete";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Deletion of {$team->display_name()} complete</h1>
<p>Deletion of team {$team->display_name()} was successful.</p>

EOT;
?>
<p>Click <a href="teamsupd.php" title="Go back to editing teams">here</a> to return to the team update menu.</p>
</div>
</div>
</body>
</html>
