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

try {
	$player = new Player();
	$player->fromget();
	$player->fetchdets();
}
catch (PlayerException $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$ret = mysql_query("delete from player where {$player->queryof()}");
if (!$ret) {
	$mess = "Cannot delete player";
	include 'php/dataerror.php';
	exit(0);
}
$nrows = mysql_affected_rows();
if ($nrows == 0) {
	$mess = "No player deleted";
	include 'php/dataerror.php';
	exit(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Deletion of {$player->display_name(false)} complete";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
print <<<EOT
<h1>Deletion of {$player->display_name(false)} complete</h1>
<p>
Deletion of player {$player->display_name(false)} was successful.</p>

EOT;
?>
<p>Click <a href="playupd.php" title="Go back to editing players">here</a> to return to the player update menu.</p>
</div>
</div>
</body>
</html>
