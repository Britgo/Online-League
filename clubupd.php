<?php
//   Copyright 2011-2021 John Collins

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

$Connection = opendatabase(true);
lg_html_header("Update clubs");
lg_html_nav();
print <<<EOT
<h1>Update Clubs</h1>
<p>Please select the club to be updated from the following list.</p>
<p>To add a club click on one at random and just change the entries on the form or
select the "new club" menu option</p>
<table class="clubupd">
<tr>
<th>Abbrev</th>
<th>Name</th>
</tr>

EOT;
$ret = $Connection->query("SELECT code FROM club ORDER BY name");
if ($ret && $ret->num_rows) {
	while ($row = $ret->fetch_assoc()) {
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
print("</table>\n");
lg_html_footer();
?>
