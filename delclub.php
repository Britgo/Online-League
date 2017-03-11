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

try {
	$club = new Club();
	$club->fromget();
	$club->fetchdets();
}
catch (ClubException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from club where {$club->queryof()}");
if (!$ret) {
	$mess = "Cannot delete club";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No clubs deleted";
	include 'php/dataerror.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Deletion of {$club->display_name()} complete";
include 'php/head.php';
print <<<EOT
<body>

EOT;
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Deletion of {$club->display_name()} complete</h1>
<p>
Deletion of club {$club->display_name()} was successful.</p>
EOT;
?>
<p>Click <a href="clubupd.php" title="Resume club update">here</a> to return to the club update menu.</p>
</div>
</div>
</body>
</html>
