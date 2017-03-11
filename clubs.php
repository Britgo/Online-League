<?php
//   Copyright 2009 John Collins

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
$Title = "Clubs";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<?php include 'php/nav.php'; ?>
<h1>Clubs</h1>
<table class="clublist">
<tr>
<th>Abbrev</th>
<th>Name</th>
<th>Contact</th>
<th>Phone</th>
<th>Email</th>
<th>Website</th>
<th>Night</th>
<th>Schools</th>
</tr>
<?php
$pemail = strlen($username) != 0;
$ret = mysql_query("select code from club order by name");
if ($ret && mysql_num_rows($ret)) {
	while ($row = mysql_fetch_assoc($ret)) {
		$p = new Club($row["code"]);
		$p->fetchdets();
		$sch = $p->Schools? 'Yes': '-';
		print <<<EOT
<tr>
<td>{$p->display_code()}</td>
<td>{$p->display_name()}</td>
<td>{$p->display_contact()}</td>
<td>{$p->display_contphone()}</td>
<td>{$p->display_contemail($pemail)}</td>
<td>{$p->display_website()}</td>
<td>{$p->display_night()}</td>
<td>$sch</td>
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
