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
include 'php/opendatabase.php';
include 'php/club.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<?php
$Title = "Update clubs";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php
$showadmmenu = true;
include 'php/nav.php';
?>
<h1>Update Clubs</h1>
<p>Please select the club to be updated from the following list.</p>
<p>To add a club click on one at random and just change the entries on the form or
select the "new club" menu option</p>
<table class="clubupd">
<tr>
<th>Abbrev</th>
<th>Name</th>
</tr>
<?php
$ret = mysql_query("select code from club order by name");
if ($ret && mysql_num_rows($ret)) {
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Club($row["code"]);
		$p->fetchdets();
		print <<<EOT
<tr>
<td><a href="updindclub.php?{$p->urlof()}" title="Update club details">{$p->display_code()}</a></td>
<td><a href="updindclub.php?{$p->urlof()}" title="Update club details">{$p->display_name()}</a></td>
</tr>
EOT;
	}
}
?>
</table>
</div>
</div>
</body>
</html>
