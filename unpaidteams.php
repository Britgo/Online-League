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
include 'php/rank.php';
include 'php/player.php';
include 'php/team.php';

$Connection = opendatabase(true);
if (!$Connection->$admin)
   wrongentry("You have to be logged in as an admin to see this page");

lg_html_header("Unpaid Teams");
lg_html_nav();

print <<<EOT
<h1>Teams which have not paid</h1>

EOT;

$ret = $Connection->query("SELECT name FROM team WHERE paid=0 AND playing!=0 ORDER BY divnum,name");
if (!$ret || $ret->num_rows == 0)  {
	print <<<EOT
<p>There does not seem to be any team which has not paid.</p>
<p>Please <a href="javascript:history.back()">click here</a> to go back.</p>

EOT;
}
else  {
	print <<<EOT
<form name="mailform" action="unpaidteams2.php" method="post" enctype="application/x-www-form-urlencoded">
<table class="teamsb">
<tr>
	<th>Send mail</th>
	<th>Name</th>
	<th>Full Name</th>
	<th>Captain</th>
</tr>

EOT;
	$num = 0;
	while ($row = $ret->fetch_array())  {
		$team = new Team($row[0]);
		$team->fetchdets();
		print <<<EOT
<tr>
<td><input type="checkbox" name="tnum[]" value="$num" checked></td>
<td>{$team->display_name()}</td>
<td>{$team->display_description()}</td>
<td>{$team->display_captain()}</td>
</tr>
EOT;
		$num++;
	}
	print <<<EOT
</table>
<p>Reply to:<input type="text" name="emailrep"></p>
<textarea name="messagetext" rows="10" cols="40"></textarea>
<br clear="all">
<input type="submit" name="submit" value="Submit message">
</form>

EOT;
}
print <<<EOT
<h2>Set all teams as unpaid</h2>
<p>At the start of the season, you will want to set all teams as not having paid.
If you want to do this now, <a href="setunpaid.php">click here</a>.</p>

EOT;
lg_html_footer();
?>
