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
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update Paid status for Team {$team->display_name()}";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Update Paid for Team {$team->display_name()}</h1>
EOT;
if ($team->Paid) {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as paid but setting to <b>unpaid</b>.
</p>
EOT;
	$v = false;
}
else {
	print <<<EOT
<p>
Team {$team->display_name()} was previously marked as unpaid.
Now setting to <b>paid</b>.
</p>
EOT;
	$v = true;
}
$team->setpaid($v);
?>
<p>Click <a href="teamsupd.php">here</a> to return to the team update menu.</p>
</div>
</div>
</body>
</html>
