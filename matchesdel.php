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
$div = $_GET["div"];
if (strlen($div) == 0) {
	include 'php/wrongentry.php';
	exit(0);
}
mysql_query("delete from lgmatch where divnum=$div");
mysql_query("delete from game where divnum=$div and result='N'");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Delete Matches complete";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Delete Matches Completed</h1>
<?php
print <<<EOT
<p>Finished deleting matches for Division $div</p>
EOT;
?>
<p>Click <a href="matchupd.php">here</a> to return to the match editing page.</p>
</div>
</div>
</body>
</html>
