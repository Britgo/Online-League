<?php
//   Copyright 2009-2021 John Collins

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

$Connection = opendatabase();
lg_html_header("Clubs");
lg_html_nav();
print <<<EOT
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

EOT;
$ret = $Connection->query("SELECT code FROM club ORDER BY name");
if ($ret && $ret->num_rows) {
	while ($row = $ret->fetch_assoc()) {
		$p = new Club($row["code"]);
		$p->fetchdets();
		$sch = $p->Schools? 'Yes': '-';
		print <<<EOT
<tr>
<td>{$p->display_code()}</td>
<td>{$p->display_name()}</td>
<td>{$p->display_contact()}</td>
<td>{$p->display_contphone()}</td>
<td>{$p->display_contemail($Connection->logged_in)}</td>
<td>{$p->display_website()}</td>
<td>{$p->display_night()}</td>
<td>$sch</td>
</tr>
EOT;
	}
}
print("</table>\n");
lg_html_footer();
?>
